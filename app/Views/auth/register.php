<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Carteira Financeira</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; padding: 8px; box-sizing: border-box;
        }
        .error { color: red; font-size: 0.9em; }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-danger { color: #a94442; background-color: #f2dede; border-color: #ebccd1; }
        .alert-success { color: #3c763d; background-color: #dff0d8; border-color: #d6e9c6; }
        button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h1>Cadastro de Novo Usuário</h1>

    <?php $validation = \Config\Services::validation(); ?>
    <?php $session = \Config\Services::session(); ?>

    <?php if ($session->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= $session->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?= form_open('/register') ?>

        <?= csrf_field() ?>

        <div class="form-group">
            <label for="name">Nome Completo:</label>
            <input type="text" name="name" id="name" value="<?= set_value('name') ?>" required>
            <?php if ($validation->hasError('name')): ?>
                <p class="error"><?= $validation->getError('name') ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= set_value('email') ?>" required>
             <?php if ($validation->hasError('email')): ?>
                <p class="error"><?= $validation->getError('email') ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Senha (mínimo 6 caracteres):</label>
            <input type="password" name="password" id="password" required>
             <?php if ($validation->hasError('password')): ?>
                <p class="error"><?= $validation->getError('password') ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirmar Senha:</label>
            <input type="password" name="password_confirm" id="password_confirm" required>
             <?php if ($validation->hasError('password_confirm')): ?>
                <p class="error"><?= $validation->getError('password_confirm') ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <button type="submit">Cadastrar</button>
        </div>

    <?= form_close() ?>

    <p>Já tem uma conta? <a href="<?= site_url('/login') ?>">Faça Login</a></p>

</body>
</html>