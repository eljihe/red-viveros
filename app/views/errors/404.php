<?php
/** @var string $mensaje */
/** @var bool $debug */
?>
<div class="panel">
    <h2>404 — Página no encontrada</h2>
    <p>La página que buscas no existe o aún no está disponible.</p>
    <?php if ($debug): ?>
        <p><code><?= e($mensaje) ?></code></p>
    <?php endif; ?>
    <p><a class="btn pri" href="<?= e(url('')) ?>">Volver al inicio</a></p>
</div>
