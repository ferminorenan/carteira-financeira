<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    private $transactionModel;
    private $userModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->userModel         = new UserModel();
    }

    private function serializeTransaction(array $transaction, int $userId): array
    {
        try {
            $tipo = match ($transaction['type']) {
                'transfer' => 'Transferência',
                'deposit'  => 'Depósito',
                default    => esc($transaction['type']),
            };

            $valor = ($transaction['type'] === 'transfer' && $transaction['sender_id'] == $userId)
                ? '-R$ ' . number_format($transaction['amount'], 2, ',', '.')
                : 'R$ ' . number_format($transaction['amount'], 2, ',', '.');

            $data = !empty($transaction['created_at'])
                ? date('d/m/Y H:i:s', strtotime($transaction['created_at']))
                : '-';

            $status = match ($transaction['status']) {
                'reversed_by_sender'   => ($transaction['sender_id'] == $userId) ? 'Cancelado por Você' : 'Cancelado pelo Pagador',
                'reversed_by_receiver' => ($transaction['receiver_id'] == $userId) ? 'Cancelado por Você' : 'Cancelado pelo Recebedor',
                'completed'            => 'Concluído',
                default                => esc($transaction['status']),
            };

            return [
                'tipo'        => $tipo,
                'valor'       => $valor,
                'data'        => $data,
                'can_reverse' => ($transaction['status'] === 'completed' && ($transaction['sender_id'] == $userId || $transaction['receiver_id'] == $userId)),
                'id'          => $transaction['id'],
                'status'      => $status,
            ];
        } catch (\Exception $e) {
            $logger = service('logger');
            $logger->error('Erro ao serializar transação: ' . $e->getMessage(), ['transaction' => $transaction, 'userId' => $userId]);
            throw $e;
        }
    }

    public function index()
    {
        $session     = session();
        $userId      = $session->get('user_id');
        $userData    = $this->userModel->find($userId);
        $users       = $this->userModel->findAll();

        if (! $userData) {
            $logger = service('logger');
            $logger->error('Dados de usuário não encontrados para o ID: ' . $userId);
            session()->destroy();
            return redirect()->to('/login')
                ->with('error', 'Erro ao buscar dados do usuário. Faça login novamente.');
        }

        try {
            $transactions = $this->transactionModel
                ->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->findAll();

            $serializedTransactions = array_map(
                fn($transaction) => $this->serializeTransaction($transaction, $userId),
                $transactions
            );
        } catch (\Exception $e) {
            $logger = service('logger');
            $logger->error('Erro ao buscar ou serializar transações: ' . $e->getMessage(), ['userId' => $userId]);
            return view('dashboard/index', [
                'userName'      => $userData['name'],
                'userId'        => $userId,
                'userBalance'   => $userData['balance'],
                'users'         => $users,
                'transactions'  => [],
                'error'         => 'Ocorreu um erro ao carregar o histórico de transações.',
            ]);
        }

        return view('dashboard/index', [
            'userName'      => $userData['name'],
            'userId'        => $userId,
            'userBalance'   => $userData['balance'],
            'users'         => $users,
            'transactions'  => $serializedTransactions,
        ]);
    }
}
