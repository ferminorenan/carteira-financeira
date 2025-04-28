<?php

namespace Tests\Integration;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use App\Models\UserModel;
use App\Models\TransactionModel;

class TransactionControllerIntegrationTest extends CIUnitTestCase
{
    use ControllerTestTrait;

    private $userModel;
    private $transactionModel;

    public function setUp(): void
    {
        parent::setUp();
        $this->userModel = new UserModel();
        $this->transactionModel = new TransactionModel();
        //  Set up any necessary database seed data or mocks
    }

    public function testDeposit()
    {
        //  Assuming user ID 1 exists
        $result = $this->withSession(['user_id' => 1])
                       ->withRequest(new \CodeIgniter\HTTP\IncomingRequest(null, null, null, 'CLI'))
                       ->controller(\App\Controllers\TransactionController::class)
                       ->execute('deposit', [], ['amount' => 100]);

        $this->assertTrue($result->isOK());
        $this->seeInDatabase('transactions', ['receiver_id' => 1, 'type' => 'deposit', 'amount' => 100]);
        $user = $this->userModel->find(1);
        $this->assertEquals(1100, $user['balance']); //  Assuming initial balance was 1000
    }

    public function testTransfer()
    {
        //  Assuming user IDs 1 and 2 exist
        $result = $this->withSession(['user_id' => 1])
                       ->withRequest(new \CodeIgniter\HTTP\IncomingRequest(null, null, null, 'CLI'))
                       ->controller(\App\Controllers\TransactionController::class)
                       ->execute('transfer', [], ['receiver_id' => 2, 'amount' => 50]);

        $this->assertTrue($result->isOK());
        $this->seeInDatabase('transactions', ['sender_id' => 1, 'receiver_id' => 2, 'type' => 'transfer', 'amount' => 50]);
        $sender = $this->userModel->find(1);
        $receiver = $this->userModel->find(2);
        $this->assertEquals(950, $sender['balance']); //  Assuming initial balance was 1000
        $this->assertEquals(1050, $receiver['balance']); //  Assuming initial balance was 1000
    }

    public function testReverseTransaction()
    {
        //  Assuming transaction with ID 1 exists and is 'completed'
        $result = $this->withSession(['user_id' => 1])
                       ->withRequest(new \CodeIgniter\HTTP\IncomingRequest(null, null, null, 'CLI'))
                       ->controller(\App\Controllers\TransactionController::class)
                       ->execute('reverse', 1);

        $this->assertTrue($result->isOK());
        $this->seeInDatabase('transactions', ['id' => 1, 'status' => 'reversed']);
        //  ...  Add assertions to check balance changes
    }
}