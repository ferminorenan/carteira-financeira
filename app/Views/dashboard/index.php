<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Carteira Financeira</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        .navbar {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #dee2e6;
        }

        .navbar a {
            text-decoration: none;
            color: #007bff;
            margin-left: 10px;
        }

        .balance {
            font-size: 1.5em;
            font-weight: bold;
            margin: 20px 0;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #3c763d;
            background: #dff0d8;
            border-color: #d6e9c6;
        }

        .alert-danger {
            color: #a94442;
            background: #f2dede;
            border-color: #ebccd1;
        }

        button {
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
        }

        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <span>Bem-vindo(a), <?= esc($userName) ?>!</span>
        <div>
            <a href="<?= site_url('/logout') ?>">Sair</a>
        </div>
    </div>

    <h1>Seu Painel</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" id="flashMessage"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" id="flashMessage"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="balance">
        Seu Saldo Atual: R$ <?= number_format($userBalance, 2, ',', '.') ?>
    </div>

    <hr>

    <div class="actions">
        <h2>Ações</h2>
        <button id="depositBtn">Realizar Depósito</button>
        <button id="transferBtn">Realizar Transferência</button>
    </div>

    <hr>

    <!-- Inclui Modais dos Partials -->
    <?= view('transaction/deposit') ?>
    <?= view('transaction/transfer') ?>
    <?= view('transaction/history') ?>

    <script>
        const depositBtn = document.getElementById('depositBtn');
        const transferBtn = document.getElementById('transferBtn');
        const depositModal = document.getElementById('depositModal');
        const transferModal = document.getElementById('transferModal');
        const closeDeposit = document.getElementById('closeDepositModal');
        const closeTransfer = document.getElementById('closeTransferModal');

        depositBtn.onclick = () => depositModal.style.display = 'block';
        transferBtn.onclick = () => transferModal.style.display = 'block';
        closeDeposit.onclick = () => {
            depositModal.style.display = 'none';
            location.reload();
        }
        closeTransfer.onclick = () => {
            transferModal.style.display = 'none';
            location.reload();
        }

        window.onclick = (e) => {
            if (e.target === depositModal) {
                depositModal.style.display = 'none';
                location.reload();
            };
            if (e.target === transferModal) {
                transferModal.style.display = 'none';
                location.reload();
            };
        };

        const flashMessage = document.getElementById('flashMessage');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.display = 'none';
            }, 5000);
        }
    </script>

</body>

</html>
