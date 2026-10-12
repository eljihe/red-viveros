<?php
/** @var \Throwable $excepcion */
/** @var bool $debug */
?>
<div class="panel">
    <h2>500 — Error interno</h2>
    <p>Ocurrió un problema al procesar tu solicitud. Intenta de nuevo más tarde.</p>
    <?php if ($debug): ?>
        <p><strong><?= e($excepcion->getMessage()) ?></strong></p>
        <p><code><?= e($excepcion->getFile()) ?>:<?= e((string) $excepcion->getLine()) ?></code></p>
        <pre><?= e($excepcion->getTraceAsString()) ?></pre>
    <?php endif; ?>
    <p><a class="btn pri" href="<?= e(url('')) ?>">Volver al inicio</a></p>
</div>
