<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Config\Database;
use CodeIgniter\HTTP\ResponseInterface;

class TransactionController extends BaseController
{
    use ResponseTrait;

    private $transactionModel;
    private $userModel;
    private $db;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->userModel = new UserModel();
        $this->db = Database::connect();
    }

    public function usersList()
    {
        $users = $this->userModel->select('id, name, email')->findAll();
        return $this->respond(['users' => $users], 200);
    }

    public function deposit()
    {
        $userId = session()->get('user_id');
        $amount = $this->request->getPost('amount');

        if (!$this->validate(['amount' => 'required|numeric|greater_than[0]'])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $this->db->transStart();

        $now = date('Y-m-d H:i:s');
        $transactionData = [
            'receiver_id'                     => $userId,
            'type'                            => 'deposit',
            'amount'                          => $amount,
            'status'                          => 'completed',
            'created_at'                      => $now,
            'updated_at'                      => $now
        ];
        $this->transactionModel->insert($transactionData);

        $user = $this->userModel->find($userId);
        if (!$user) {
            $this->db->transRollback();
            return $this->fail("Usuário não encontrado.", 400);
        }
        $newBalance = $user['balance'] + $amount;
        $this->userModel->update($userId, ['balance' => $newBalance]);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return $this->failServerError('Erro ao realizar depósito.');
        }

        return $this->respond(['message' => 'Depósito realizado com sucesso.'], 200);
    }

    public function transfer()
    {
        $senderId   = session()->get('user_id');
        $receiverId = $this->request->getPost('receiver_id');
        $amount     = $this->request->getPost('amount');

        if (!$this->validate([
            'receiver_id' => 'required|numeric',
            'amount'      => 'required|numeric|greater_than[0]'
        ])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($senderId == $receiverId) {
            return $this->fail("Remetente e destinatário não podem ser o mesmo.", 400);
        }

        $this->db->transStart();

        $sender   = $this->userModel->find($senderId);
        $receiver = $this->userModel->find($receiverId);

        if (!$sender) {
            $this->db->transRollback();
            return $this->fail("Remetente não encontrado.", 400);
        }
        if ($sender['balance'] < $amount) {
            $this->db->transRollback();
            return $this->fail("Saldo insuficiente.", 400);
        }
        if (!$receiver) {
            $this->db->transRollback();
            return $this->fail("Destinatário não encontrado.", 400);
        }

        $this->userModel->update($senderId, ['balance' => $sender['balance'] - $amount]);
        $this->userModel->update($receiverId, ['balance' => $receiver['balance'] + $amount]);

        $now = date('Y-m-d H:i:s');
        $transactionData = [
            'sender_id'                     => $senderId,
            'receiver_id'                   => $receiverId,
            'type'                            => 'transfer',
            'amount'                          => $amount,
            'status'                          => 'completed',
            'created_at'                      => $now,
            'updated_at'                      => $now
        ];
        $this->transactionModel->insert($transactionData);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return $this->failServerError('Erro ao realizar transferência.');
        }

        return $this->respond(['message' => 'Transferência realizada com sucesso.'], 200);
    }

    public function history()
    {
        $userId = session()->get('user_id');
        $transactions = $this->transactionModel
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        array_walk($transactions, function (&$tx) use ($userId) {
            $tx['can_reverse'] = ($tx['status'] === 'completed' && ($tx['sender_id'] == $userId || $tx['receiver_id'] == $userId));
        });

        return $this->respond(['transactions' => $transactions], 200);
    }

    public function reverse(int $transactionId)
    {
        $userId = session()->get('user_id');
        $transaction = $this->transactionModel->find($transactionId);

        if (!$transaction) {
            return $this->fail("Transação não encontrada.", 404);
        }
        if ($transaction['status'] !== 'completed') {
            return $this->fail("Transação não pode ser revertida.", 400);
        }

        $this->db->transStart();
        $now = date('Y-m-d H:i:s');
        $reverseStatus = 'reversed';

        if ($transaction['type'] == 'transfer') {
            if ($transaction['sender_id'] == $userId) {
                $sender   = $this->userModel->find($transaction['sender_id']);
                $receiver = $this->userModel->find($transaction['receiver_id']);
                if ($sender)   $this->userModel->update($transaction['sender_id'], ['balance' => $sender['balance'] + $transaction['amount']]);
                if ($receiver) $this->userModel->update($transaction['receiver_id'], ['balance' => $receiver['balance'] - $transaction['amount']]);
                $reverseStatus = 'reversed_by_sender';
            } elseif ($transaction['receiver_id'] == $userId) {
                $sender   = $this->userModel->find($transaction['sender_id']);
                $receiver = $this->userModel->find($transaction['receiver_id']);
                if ($sender)   $this->userModel->update($transaction['sender_id'], ['balance' => $sender['balance'] - $transaction['amount']]);
                if ($receiver) $this->userModel->update($transaction['receiver_id'], ['balance' => $receiver['balance'] + $transaction['amount']]);
                $reverseStatus = 'reversed_by_receiver';
            } else {
                $this->db->transRollback();
                return $this->fail("Você não tem permissão para reverter esta transação.", 403);
            }
        } elseif ($transaction['type'] == 'deposit') {
            if ($transaction['receiver_id'] == $userId) {
                $user = $this->userModel->find($transaction['receiver_id']);
                if ($user) $this->userModel->update($transaction['receiver_id'], ['balance' => $user['balance'] - $transaction['amount']]);
                $reverseStatus = 'reversed_by_receiver';
            } else {
                $this->db->transRollback();
                return $this->fail("Você não tem permissão para reverter este depósito.", 403);
            }
        }

        $this->transactionModel->update($transactionId, [
            'status'     => $reverseStatus,
            'updated_at' => $now
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return $this->failServerError('Erro ao reverter transação.');
        }

        return $this->respond(['message' => 'Transação revertida com sucesso.'], 200);
    }
}

