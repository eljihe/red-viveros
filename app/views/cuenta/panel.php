<?php
/** @var array<string, mixed> $usuario */
/** @var array<string, mixed>|null $vivero */
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
    <?php if ($vivero !== null): ?>
        <div class="panel" style="background:var(--surface-2); margin-top:16px;">
            <strong><?= e($vivero['nombre']) ?></strong>
            <p style="margin:0; font-size:.92rem;">
                Estado:
                <?php if ($vivero['estado'] === 'pendiente'): ?>
                    <span style="color:var(--warn);">Pendiente de aprobación</span>
                <?php elseif ($vivero['estado'] === 'aprobado'): ?>
                    <span style="color:var(--ok);">Aprobado</span>
                <?php elseif ($vivero['estado'] === 'rechazado'): ?>
                    <span style="color:var(--err);">Rechazado</span>
                    <?php if ($vivero['motivo_estado']): ?> — <?= e($vivero['motivo_estado']) ?><?php endif; ?>
                <?php elseif ($vivero['estado'] === 'suspendido'): ?>
                    <span style="color:var(--err);">Suspendido</span>
                    <?php if ($vivero['motivo_estado']): ?> — <?= e($vivero['motivo_estado']) ?><?php endif; ?>
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
    <div class="actions">
        <?php if ($vivero !== null): ?>
            <a href="<?= e(url('panel/vivero')) ?>" class="btn">Editar mi vivero</a>
        <?php endif; ?>
        <a href="<?= e(url('cuenta/cambiar-pin')) ?>" class="btn">Cambiar PIN</a>
        <form method="POST" action="<?= e(url('cuenta/salir')) ?>" style="display:inline-flex;">
            <?= csrfField() ?>
            <button type="submit" class="btn">Cerrar sesión</button>
        </form>
    </div>
</div>
