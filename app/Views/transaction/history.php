<h2>Histórico de Transações</h2>
<div id="transactionMessage" class="alert" style="display:none;"></div>
<table>
    <thead>
        <tr>
            <th>Tipo</th>
            <th>Valor</th>
            <th>Status</th>
            <th>Data</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($transactions)): ?>
            <?php foreach ($transactions as $tx): ?>
                <tr>
                    <td><?= esc($tx['tipo']) ?></td>
                    <td><?= esc($tx['valor']) ?></td>
                    <td><?= esc($tx['data']) ?></td>
                    <td><?= esc($tx['status']) ?></td>
                    <td>
                        <?php if (!empty($tx['can_reverse']) && $tx['can_reverse']): ?>
                            <a
                                href="javascript:void(0);"
                                class="btn-reverse"
                                data-transaction-id="<?= $tx['id'] ?>">
                                Cancelar
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Nenhuma transação encontrada.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background: #f2f2f2;
    }

    .btn-reverse {
        padding: 6px 12px;
        background: #dc3545;
        color: #fff;
        border-radius: 4px;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-reverse:hover {
        background-color: #c82333;
    }

    .alert {
        margin-top: 10px;
        padding: 10px;
        border-radius: 4px;
        border: 1px solid;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    .alert-secondary {
        color: #383d41;
        background-color: #e2e3e4;
        border-color: #d3d6da;
    }
</style>
<script>
    const transactionMessage = document.getElementById('transactionMessage');
    const reverseButtons = document.querySelectorAll('.btn-reverse');

    reverseButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const transactionId = button.dataset.transactionId;
            transactionMessage.innerHTML = '';
            transactionMessage.className = 'alert';
            transactionMessage.style.display = 'block';

            const confirmation = confirm('Deseja realmente reverter esta transação?');
            if (!confirmation) {
                transactionMessage.innerHTML = 'Operação de reversão cancelada.';
                transactionMessage.classList.add('alert-secondary');
                return;
            }

            try {
                const response = await fetch(`<?= site_url('transaction/reverse/') ?>/${transactionId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    transactionMessage.innerHTML = data.message;
                    transactionMessage.classList.add('alert-success');
                    const row = button.closest('tr');
                    if (row) {
                        row.remove();
                    }

                } else {
                    const errors = data.messages || data.errors || data;
                    const msg = Array.isArray(errors) ?
                        errors.join('<br>') :
                        (typeof errors === 'object' ?
                            Object.values(errors).join('<br>') :
                            errors);
                    transactionMessage.innerHTML = msg;
                    transactionMessage.classList.add('alert-danger');
                }
            } catch (error) {
                console.error('Erro na reversão:', error);
                transactionMessage.innerHTML = 'Erro ao reverter transação.';
                transactionMessage.classList.add('alert-danger');
            }
        });
    });
</script>