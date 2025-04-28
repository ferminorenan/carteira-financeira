<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\TransactionModel;

class TransactionModelTest extends CIUnitTestCase
{
    protected $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new TransactionModel();
    }

    public function testInsertTransaction()
    {
        $transactionData = [
            'sender_id' => 1,
            'receiver_id' => 2,
            'type' => 'transfer',
            'amount' => 50,
            'status' => 'completed',
        ];
        $id = $this->model->insert($transactionData);
        $this->assertIsInt($id);
        $transaction = $this->model->find($id);
        $this->assertEquals(50, $transaction['amount']);
        $this->assertEquals('transfer', $transaction['type']);
    }

    public function testUpdateTransactionStatus()
    {
        //  Assuming you have a transaction with ID 1
        $this->model->update(1, ['status' => 'reversed']);
        $transaction = $this->model->find(1);
        $this->assertEquals('reversed', $transaction['status']);
    }
}