<?php
namespace App\Controllers;

use App\Models\UserModel; // Importa o UserModel
use CodeIgniter\Controller;

class AuthController extends BaseController
{
    public function registerForm()
    {
        helper(['form']);
        return view('auth/register');
    }

    public function register()
    {
        helper(['form']);

        $rules = [
            'name' => 'required|min_length[3]|max_length[50]',
            'email' => 'required|min_length[6]|max_length[100]|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]|max_length[255]',
            'password_confirm' => 'matches[password]'
        ];

        if (!$this->validate($rules)) {

            return view('auth/register', [
                'validation' => $this->validator
            ]);
        } else {
            $userModel = new UserModel();
            $data = [
                'name' => $this->request->getVar('name'),
                'email' => $this->request->getVar('email'),
                'password' => $this->request->getVar('password'),
                'balance' => 0.00
            ];

            if ($userModel->save($data)) {
                return redirect()->to('/login')->with('success', 'Cadastro realizado com sucesso! Faça o login.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Erro ao tentar cadastrar. Tente novamente.');
            }
        }
    }

    public function loginForm()
    {
        helper(['form']);
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }
        return view('auth/login');
    }
    public function login()
    {
        helper(['form']);
        $session = session();
        $userModel = new UserModel();

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            $sessionData = [
                'user_id' => $user['id'],
                'user_name' => $user['name'],
                'user_email' => $user['email'],
                'isLoggedIn' => TRUE
            ];
            $session->set($sessionData);
            return redirect()->to('/')->with('success', 'Login bem-sucedido!');

        } else {
            return redirect()->back()->withInput()->with('error', 'Email ou senha inválidos.');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Você foi desconectado.');
    }
}