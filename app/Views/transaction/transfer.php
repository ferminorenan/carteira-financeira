<div id="transferModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeTransferModal">&times;</span>
        <h2>Realizar Transferência</h2>
        <form id="transferForm">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="receiverId">Destinatário:</label>
                <select id="receiverId" name="receiver_id" required>
                    <?php foreach ($users as $user): ?>
                        <?php if ($user['id'] != session()->get('user_id')): ?>
                            <option value="<?= esc($user['id']) ?>">
                                <?= esc($user['name']) ?> (<?= esc($user['email']) ?>)
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="transferAmount">Valor da Transferência:</label>
                <input type="number" id="transferAmount" name="amount" step="0.01" required>
            </div>

            <button type="submit">Transferir</button>
        </form>
        <div id="transferMessage" class="alert"></div>
    </div>
</div>

<script>
    const transferForm = document.getElementById('transferForm');
    const transferMessage = document.getElementById('transferMessage');

    transferForm.addEventListener('submit', async event => {
        event.preventDefault();
        transferMessage.innerHTML = '';
        transferMessage.className = 'alert';

        const formData = new URLSearchParams(new FormData(transferForm));

        try {
            const response = await fetch('<?= site_url('transaction/transfer') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            });
            const data = await response.json();

            if (response.ok) {
                transferMessage.innerHTML = data.message;
                transferMessage.classList.add('alert-success');
            } else {
                const errors = data.messages || data.errors || data;
                const msg = Array.isArray(errors)
                    ? errors.join('<br>')
                    : (typeof errors === 'object'
                        ? Object.values(errors).join('<br>')
                        : errors);
                transferMessage.innerHTML = msg;
                transferMessage.classList.add('alert-danger');
            }
        } catch (error) {
            console.error('Erro na transferência:', error);
            transferMessage.innerHTML = 'Erro ao realizar transferência.';
            transferMessage.classList.add('alert-danger');
        }
    });
</script>
