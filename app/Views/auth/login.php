<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Carteira Financeira</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="email"], input[type="password"] {
            width: 100%; padding: 8px; box-sizing: border-box;
        }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-danger { color: #a94442; background-color: #f2dede; border-color: #ebccd1; }
        .alert-success { color: #3c763d; background-color: #dff0d8; border-color: #d6e9c6; }
         button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h1>Login</h1>

    <?php $session = \Config\Services::session(); ?>

    <?php if ($session->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= $session->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if ($session->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= $session->getFlashdata('success') ?></div>
    <?php endif; ?>


    <?= form_open('/login') ?>
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= set_value('email') ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Senha:</label>
            <input type="password" name="password" id="password" required>
        </div>

        <div class="form-group">
            <button type="submit">Entrar</button>
        </div>

    <?= form_close() ?>

    <p>Não tem uma conta? <a href="<?= site_url('/register') ?>">Cadastre-se</a></p>

</body>
</html>