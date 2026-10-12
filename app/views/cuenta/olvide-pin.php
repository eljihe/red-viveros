<?php
/** @var string|null $enlaceWhatsApp */
?>
<div class="panel">
    <h2>Olvidé mi PIN</h2>
    <p>Para restablecer tu PIN, escribe al administrador por WhatsApp.</p>
    <?php if ($enlaceWhatsApp): ?>
        <a href="<?= e($enlaceWhatsApp) ?>" class="btn pri" target="_blank">Contactar por WhatsApp</a>
    <?php else: ?>
        <p style="color: var(--muted);">No hay un administrador disponible en este momento.</p>
    <?php endif; ?>
    <p><a href="<?= e(url('cuenta')) ?>">Volver</a></p>
</div>
