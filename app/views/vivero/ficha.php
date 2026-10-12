<?php
/** @var array<string, mixed> $vivero */
/** @var array<int, array<string, mixed>> $productos */
/** @var bool $telefonoVisible */

$comoLlegar = externalUrl('maps.google') . '?q=' . $vivero['lat'] . ',' . $vivero['lng'];
$fotos = $vivero['fotos'];

$totalPlantas = 0;
foreach ($vivero['inventario'] as $fila) {
    $totalPlantas += (int) $fila['cantidad'];
}

$ofreceEntregas = false;
foreach ($productos as $p) {
    if ((int) $p['hace_entrega'] === 1) {
        $ofreceEntregas = true;
        break;
    }
}

$iconosPlantas = ['🌳', '🍫', '🍋', '🌿', '🥭', '🥥'];
?>
<div class="vivero-page">
    <p><a href="<?= e(url('')) ?>" class="back">← Volver al mapa de viveros</a></p>

    <div class="vhero">
        <?php if (!empty($fotos)): ?>
            <img class="photo" src="<?= e(url('uploads/viveros/' . basename($fotos[0]))) ?>" alt="Foto de <?= e($vivero['nombre']) ?>">
        <?php else: ?>
            <div class="ph">Sin foto</div>
        <?php endif; ?>
        <div class="vhero-caption"><?= e($vivero['comunidad']) ?>, <?= e($vivero['municipio']) ?></div>
    </div>

    <?php if (count($fotos) > 1): ?>
        <div class="vgallery" aria-label="Galería de fotos">
            <?php foreach (array_slice($fotos, 1, 3) as $foto): ?>
                <div class="vthumb"><img src="<?= e(url('uploads/viveros/' . basename($foto))) ?>" alt="Foto de <?= e($vivero['nombre']) ?>" loading="lazy"></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="panel">
        <div class="vpage-head">
            <div>
                <div class="meta">VIVERO COMUNITARIO · <?= e($vivero['municipio']) ?></div>
                <h2><?= e($vivero['nombre']) ?></h2>
                <div class="meta">
                    <?= e($vivero['comunidad']) ?><?php if ($vivero['integrantes']): ?> · <?= e((string) $vivero['integrantes']) ?> integrantes<?php endif; ?>
                </div>
                <div class="vchips">
                    <span class="vchip">✓ Vivero registrado</span>
                    <?php if ($ofreceEntregas): ?><span class="vchip">🚚 Ofrece entregas</span><?php endif; ?>
                    <span class="vchip">🌳 <?= e((string) count($vivero['inventario'])) ?> especies</span>
                </div>
            </div>
        </div>
        <?php if ($vivero['historia']): ?>
            <p><?= e($vivero['historia']) ?></p>
        <?php else: ?>
            <p class="meta" style="margin: 0;">Conoce las plantas disponibles y contacta al vivero para consultar existencias, precios y condiciones de entrega.</p>
        <?php endif; ?>
    </div>

    <div class="vquick">
        <div class="panel"><strong><?= e((string) $totalPlantas) ?></strong><span class="meta">Plantas registradas</span></div>
        <div class="panel"><strong><?= e((string) count($vivero['inventario'])) ?></strong><span class="meta">Especies</span></div>
        <div class="panel"><strong><?= e((string) ($vivero['integrantes'] ?? 0)) ?></strong><span class="meta">Integrantes</span></div>
    </div>

    <div class="panel">
        <h3 class="vsection-title">🌱 Plantas disponibles</h3>
        <?php if (!empty($vivero['inventario'])): ?>
            <div class="vplant-grid">
                <?php foreach ($vivero['inventario'] as $i => $fila): ?>
                    <div class="vplant">
                        <span class="plant-icon"><?= $iconosPlantas[$i % count($iconosPlantas)] ?></span>
                        <b><?= e($fila['especie_nombre']) ?></b>
                        <span><?= e((string) $fila['cantidad']) ?> plantas</span>
                        <span class="meta"><?= $fila['edad_meses'] !== null ? 'Edad: ' . e((string) $fila['edad_meses']) . ' meses' : 'Edad no registrada' ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty">El vivero aún no registra plantas.</div>
        <?php endif; ?>
    </div>

    <?php if (!empty($vivero['servicios'])): ?>
        <div class="panel">
            <h3 class="vsection-title">🧺 Talleres y servicios</h3>
            <div class="vservice-list">
                <?php foreach ($vivero['servicios'] as $s): ?>
                    <div class="vservice">
                        <span class="ico">🧺</span>
                        <div>
                            <b><?= e($s['titulo']) ?></b>
                            <?php $detalles = array_filter([$s['horario'], $s['costo']]); ?>
                            <?php if ($detalles): ?><div class="meta"><?= e(implode(' · ', $detalles)) ?></div><?php endif; ?>
                            <?php if ($s['descripcion']): ?><div class="meta"><?= e($s['descripcion']) ?></div><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="hint">Los servicios y condiciones deben confirmarse directamente con el vivero.</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($productos)): ?>
        <div class="panel">
            <h3 class="vsection-title">🛒 Productos de este vivero</h3>
            <div class="grid">
                <?php foreach ($productos as $p): ?>
                    <a href="<?= e(url('mercado/producto?id=' . (int) $p['id'])) ?>" class="card">
                        <?php if ($p['foto_portada']): ?>
                            <img class="photo" src="<?= e(url('uploads/productos/' . basename($p['foto_portada']))) ?>" alt="<?= e($p['titulo']) ?>">
                        <?php else: ?>
                            <div class="ph">Sin foto</div>
                        <?php endif; ?>
                        <div class="body">
                            <h4><?= e($p['titulo']) ?></h4>
                            <?php if ($p['precio'] !== null): ?>
                                <div class="price">$<?= e(number_format((float) $p['precio'], 2)) ?> <small>/ <?= e($p['unidad']) ?></small></div>
                            <?php else: ?>
                                <div class="hint">Precio a tratar</div>
                            <?php endif; ?>
                            <span class="meta"><?= $p['disponible'] !== null ? e((string) $p['disponible']) . ' disponibles' : 'Consultar disponibilidad' ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="panel">
        <h3 class="vsection-title">📍 Ubicación</h3>
        <div class="vlocation">
            <div>
                <div><b><?= e($vivero['comunidad']) ?>, <?= e($vivero['municipio']) ?></b></div>
                <div class="coord"><?= e((string) $vivero['lat']) ?>, <?= e((string) $vivero['lng']) ?></div>
            </div>
            <a class="btn pri big" href="<?= e($comoLlegar) ?>" target="_blank" rel="noopener">Cómo llegar ↗</a>
        </div>
    </div>

    <?php if ($telefonoVisible): ?>
        <div class="gate">
            <b>Contacto</b>
            <span style="font-family: var(--f-mono); user-select: all;"><?= e($vivero['telefono']) ?></span>
            <div class="actions">
                <a class="btn wa" href="<?= e(externalUrl('whatsapp') . '52' . $vivero['telefono'] . '?text=' . rawurlencode('Hola, vi el vivero "' . $vivero['nombre'] . '" en la Red de Viveros Tabasco y quisiera más información sobre sus plantas.')) ?>" target="_blank" rel="noopener">Escribir por WhatsApp</a>
            </div>
        </div>
    <?php else: ?>
        <div class="gate">
            <b>Para ver el contacto necesita estar registrado</b>
            <span class="hint">El teléfono y WhatsApp solo se muestran a usuarios registrados. Registrarse es gratis.</span>
            <div class="actions"><a href="<?= e(url('registro')) ?>" class="btn pri">Registrarme</a></div>
        </div>
    <?php endif; ?>

    <p class="hint">Antes de trasladarte, confirma existencias, precios y horario con el responsable.</p>
</div>
