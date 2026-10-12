<?php
/** @var array<string, mixed> $usuario */
/** @var string|null $mensaje */
/** @var string|null $tipo */
?>
<div class="panel">
    <h2>Hola, <?= e($usuario['nombre']) ?></h2>
    <p><strong>Rol:</strong> <?= e($usuario['rol_nombre']) ?></p>
    <?php if ($mensaje): ?>
        <div class="msg <?= e($tipo === 'ok' ? 'ok' : 'err') ?>">
            <?= e((string) $mensaje) ?>
        </div>
    <?php endif; ?>
    <div class="actions">
        <a href="<?= e(url('cuenta/cambiar-pin')) ?>" class="btn">Cambiar PIN</a>
        <form method="POST" action="<?= e(url('cuenta/salir')) ?>" style="display:inline-flex;">
            <?= csrfField() ?>
            <button type="submit" class="btn">Cerrar sesión</button>
        </form>
    </div>
</div>
