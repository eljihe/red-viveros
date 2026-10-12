<?php
/** @var array<string, mixed> $usuario */
/** @var array<string, mixed>|null $vivero */
/** @var array<int, array<string, mixed>> $productos */
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
            <a href="<?= e(url('panel/productos/nuevo')) ?>" class="btn pri">+ Nuevo producto</a>
        <?php endif; ?>
        <a href="<?= e(url('cuenta/cambiar-pin')) ?>" class="btn">Cambiar PIN</a>
        <form method="POST" action="<?= e(url('cuenta/salir')) ?>" style="display:inline-flex;">
            <?= csrfField() ?>
            <button type="submit" class="btn">Cerrar sesión</button>
        </form>
    </div>

    <?php if ($vivero !== null): ?>
        <div style="margin-top:24px;">
            <h3>Mis productos</h3>
            <?php if (!empty($productos)): ?>
                <div class="vlist">
                    <?php foreach ($productos as $p): ?>
                        <div class="myp">
                            <?php if ($p['foto_portada']): ?>
                                <img class="t" src="<?= e(url('uploads/productos/' . basename($p['foto_portada']))) ?>" alt="">
                            <?php else: ?>
                                <div class="t ph" style="width:56px; height:56px; aspect-ratio:auto;"></div>
                            <?php endif; ?>
                            <div>
                                <strong><?= e($p['titulo']) ?></strong>
                                <div class="hint">
                                    <?= $p['precio'] !== null ? '$' . e(number_format((float) $p['precio'], 2)) . ' / ' . e($p['unidad']) : 'Precio a tratar' ?>
                                    <?php if ((int) $p['activo'] === 0): ?> · <span style="color:var(--muted);">Pausado</span><?php endif; ?>
                                </div>
                            </div>
                            <div class="actions">
                                <a href="<?= e(url('panel/productos/editar?id=' . (int) $p['id'])) ?>" class="btn" style="font-size:.85em;">Editar</a>
                                <form method="POST" action="<?= e(url('panel/productos/eliminar')) ?>" style="display:inline;" onsubmit="return confirm('¿Eliminar este producto?');">
                                    <input type="hidden" name="id" value="<?= e((string) $p['id']) ?>">
                                    <?= csrfField() ?>
                                    <button type="submit" class="btn" style="font-size:.85em;">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="empty">Aún no publicas productos. Usa "+ Nuevo producto" para empezar a vender.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
