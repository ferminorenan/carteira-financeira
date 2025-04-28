<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Rotas de Autenticação
$routes->get('/register', 'AuthController::registerForm');
$routes->post('/register', 'AuthController::register');
$routes->get('/login', 'AuthController::loginForm');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/', 'DashboardController::index', ['filter' => 'auth']);
$routes->post('/transaction/deposit', 'TransactionController::deposit', ['filter' => 'auth']);
$routes->post('/transaction/transfer', 'TransactionController::transfer', ['filter' => 'auth']);
$routes->get('/transaction/reverse/(:num)', 'TransactionController::reverse/$1', ['filter' => 'auth']);
$routes->get('/transaction/history', 'TransactionController::history', ['filter' => 'auth']);
