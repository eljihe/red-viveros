<?php
/** @var int $municipios */
/** @var int $especies */
/** @var string|null $error */
?>
<div class="panel" style="text-align: center; max-width: 600px; margin: 0 auto;">
    <h2>Red de Viveros Tabasco</h2>
    <p style="font-size: 1.1rem; color: var(--muted);">Plataforma de venta directa de plantas · Viveros comunitarios de Sembrando Vida</p>

    <?php if ($error === null): ?>
        <div style="margin: 20px 0; padding: 15px; background: var(--surface-2); border-radius: 8px;">
            <p><strong><?= e((string) $municipios) ?></strong> municipios · <strong><?= e((string) $especies) ?></strong> especies disponibles</p>
        </div>
    <?php endif; ?>

    <div class="actions" style="justify-content: center; gap: 15px; margin-top: 30px;">
        <a href="<?= e(url('registro')) ?>" class="btn pri" style="min-width: 150px;">Registrarme</a>
        <a href="<?= e(url('cuenta')) ?>" class="btn" style="min-width: 150px;">Mi cuenta</a>
    </div>

    <p style="margin-top: 30px; color: var(--muted); font-size: 0.9rem;">
        <a href="<?= e(url('')) ?>" style="color: inherit;">Mapa de viveros</a> ·
        <a href="<?= e(url('mercado')) ?>" style="color: inherit;">Mercado</a>
    </p>
</div>
