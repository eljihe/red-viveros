<?php
/** @var string|null $mensaje */
/** @var string|null $tipo */
?>
<div class="panel">
    <h2>Entrar a mi cuenta</h2>
    <?php if ($mensaje): ?>
        <div class="msg <?= e($tipo === 'ok' ? 'ok' : 'err') ?>">
            <?= e((string) $mensaje) ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="<?= e(url('cuenta/entrar')) ?>">
        <div class="field">
            <span>Teléfono</span>
            <input type="text" name="telefono" inputmode="numeric" placeholder="1234567890" maxlength="10" required>
        </div>
        <div class="field">
            <span>PIN</span>
            <input type="text" name="pin" inputmode="numeric" placeholder="000000" maxlength="6" required>
        </div>
        <?= csrfField() ?>
        <button type="submit" class="btn pri">Entrar</button>
    </form>
    <p><a href="<?= e(url('cuenta/olvide-pin')) ?>">Olvidé mi PIN</a></p>
</div>
