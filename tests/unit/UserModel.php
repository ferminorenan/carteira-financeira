<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\UserModel;

class UserModelTest extends CIUnitTestCase
{

    protected $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new UserModel();
    }

    public function testHashPassword()
    {
        $data = ['data' => ['password' => 'mysecret']];
        $result = $this->model->hashPassword($data);
        $this->assertNotEmpty($result['data']['password']);
        $this->assertTrue(password_verify('mysecret', $result['data']['password']));
    }

    public function testCreateUser()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'testpass',
        ];
        $id = $this->model->insert($userData);
        $this->assertIsInt($id);
        $user = $this->model->find($id);
        $this->assertEquals('Test User', $user['name']);
        $this->assertEquals('test@example.com', $user['email']);
    }

    public function testUpdateUserBalance()
    {
        //  Assuming you have a user with ID 1
        $this->model->update(1, ['balance' => 100]);
        $user = $this->model->find(1);
        $this->assertEquals(100, $user['balance']);
    }
}