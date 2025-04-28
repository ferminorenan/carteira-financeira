<?php // app/Views/transaction/deposit.php ?>
<div id="depositModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeDepositModal">&times;</span>
        <h2>Realizar Depósito</h2>
        <form id="depositForm">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="depositAmount">Valor do Depósito:</label>
                <input type="number" id="depositAmount" name="amount" step="0.01" required>
            </div>
            <button type="submit">Depositar</button>
        </form>
        <div id="depositMessage" class="alert"></div>
    </div>
</div>

<script>
    const depositForm = document.getElementById('depositForm');
    const depositMessage = document.getElementById('depositMessage');
    depositForm.addEventListener('submit', async event => {
        event.preventDefault();
        depositMessage.innerHTML = '';
        depositMessage.className = 'alert';

        const formData = new URLSearchParams(new FormData(depositForm));

        try {
            const response = await fetch('<?= site_url('transaction/deposit') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            });
            const data = await response.json();

            if (response.ok) {
                depositMessage.innerHTML = data.message;
                depositMessage.classList.add('alert-success');
            } else {
                const errors = data.messages || data.errors || data;
                const msg = Array.isArray(errors)
                    ? errors.join('<br>')
                    : (typeof errors === 'object'
                        ? Object.values(errors).join('<br>')
                        : errors);
                depositMessage.innerHTML = msg;
                depositMessage.classList.add('alert-danger');
            }
        } catch (error) {
            console.error('Erro no depósito:', error);
            depositMessage.innerHTML = 'Erro ao realizar depósito.';
            depositMessage.classList.add('alert-danger');
        }
    });
</script>
