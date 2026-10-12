<?php
/** @var string|null $mensaje */
/** @var string|null $tipo */
?>
<div class="panel">
    <h2>Cambiar mi PIN</h2>
    <?php if ($mensaje): ?>
        <div class="msg <?= e($tipo === 'ok' ? 'ok' : 'err') ?>">
            <?= e((string) $mensaje) ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="<?= e(url('cuenta/cambiar-pin')) ?>">
        <div class="field">
            <span>PIN nuevo</span>
            <input type="text" name="pin_nuevo" inputmode="numeric" placeholder="000000" maxlength="6" required>
        </div>
        <div class="field">
            <span>Confirmar PIN</span>
            <input type="text" name="pin_confirmacion" inputmode="numeric" placeholder="000000" maxlength="6" required>
        </div>
        <?= csrfField() ?>
        <button type="submit" class="btn pri">Guardar PIN</button>
    </form>
</div>
