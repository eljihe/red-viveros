<?php
/** @var array<string, mixed> $producto */
/** @var array<int, array<string, mixed>> $fotos */
/** @var array<int, array<string, mixed>> $masDeEsteVivero */
/** @var float|null $distanciaKm */
/** @var bool|null $llegaAZona */
/** @var array{estado: string, mensaje?: string} $contacto */

$comoLlegar = externalUrl('maps.google') . '?q=' . $producto['lat'] . ',' . $producto['lng'];
?>
<p><a href="<?= e(url('mercado')) ?>" class="back">← Volver al mercado</a></p>

<div class="pd">
    <div>
        <?php if (!empty($fotos)): ?>
            <img class="photo" src="<?= e(url('uploads/productos/' . basename($fotos[0]['archivo']))) ?>" alt="<?= e($producto['titulo']) ?>">
            <?php if (count($fotos) > 1): ?>
                <div class="fotos-grid" style="margin-top: 10px;">
                    <?php foreach (array_slice($fotos, 1) as $foto): ?>
                        <div class="foto-item">
                            <img src="<?= e(url('uploads/productos/' . basename($foto['archivo']))) ?>" alt="<?= e($producto['titulo']) ?>" loading="lazy">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="ph">Sin foto</div>
        <?php endif; ?>
    </div>
    <div class="panel">
        <span class="tag"><?= e($producto['categoria']) ?></span>
        <h2><?= e($producto['titulo']) ?></h2>
        <?php if ($producto['precio'] !== null): ?>
            <div class="price" style="font-size: 1.6rem;">$<?= e(number_format((float) $producto['precio'], 2)) ?> <small>por <?= e($producto['unidad']) ?></small></div>
        <?php else: ?>
            <div class="hint">Precio a tratar con el vivero</div>
        <?php endif; ?>
        <?php if ($producto['descripcion']): ?>
            <p style="margin: 0;"><?= e($producto['descripcion']) ?></p>
        <?php endif; ?>

        <dl class="facts">
            <?php if ($producto['disponible'] !== null): ?>
                <dt>Disponible</dt>
                <dd><?= e((string) $producto['disponible']) ?> <?= e((int) $producto['disponible'] === 1 ? $producto['unidad'] : $producto['unidad_plural']) ?></dd>
            <?php endif; ?>
            <dt>Vivero</dt>
            <dd><?= e($producto['vivero_nombre']) ?></dd>
            <dt>Ubicación</dt>
            <dd><?= e($producto['comunidad']) ?>, <?= e($producto['municipio']) ?></dd>
            <dt>Entrega</dt>
            <dd>
                <?php if ((int) $producto['hace_entrega'] === 1): ?>
                    Sí, hasta <?= e((string) $producto['entrega_km']) ?> km<?php if ($producto['entrega_costo']): ?> · <?= e($producto['entrega_costo']) ?><?php endif; ?>
                <?php else: ?>
                    No. Se recoge en el vivero
                <?php endif; ?>
            </dd>
        </dl>

        <?php if ($distanciaKm !== null): ?>
            <p class="hint">
                Estás a <?= $distanciaKm < 1 ? 'menos de 1' : e((string) round($distanciaKm)) ?> km de este vivero
                <?php if ($llegaAZona === true): ?> · <strong style="color: var(--ok);">Llega a tu zona</strong>
                <?php elseif ($llegaAZona === false): ?> · no llega a tu zona
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <?php if ($contacto['estado'] === 'sin_sesion'): ?>
            <div class="gate">
                <b>Para ver el contacto necesita estar registrado</b>
                <span class="hint">El teléfono y WhatsApp solo se muestran a viveros y compradores registrados. Registrarse es gratis.</span>
                <div class="actions"><a href="<?= e(url('registro')) ?>" class="btn pri">Registrarme</a></div>
            </div>
        <?php elseif ($contacto['estado'] === 'limite'): ?>
            <div class="gate">
                <b>Contacto</b>
                <span class="hint"><?= e($contacto['mensaje'] ?? '') ?></span>
            </div>
        <?php else: ?>
            <div class="gate">
                <b>Contacto</b>
                <span style="font-family: var(--f-mono); user-select: all;"><?= e($producto['vivero_telefono']) ?></span>
                <div class="actions">
                    <a class="btn wa" href="<?= e(externalUrl('whatsapp') . '52' . $producto['vivero_telefono'] . '?text=' . rawurlencode('Hola, me interesa "' . $producto['titulo'] . '" que publicaron en la Red de Viveros Tabasco.')) ?>" target="_blank" rel="noopener">Escribir por WhatsApp</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="actions">
            <a class="btn" href="<?= e($comoLlegar) ?>" target="_blank" rel="noopener">Cómo llegar</a>
        </div>
    </div>
</div>

<?php if (!empty($masDeEsteVivero)): ?>
    <h3 style="font-family: var(--f-display); margin: 24px 0 10px;">Más de <?= e($producto['vivero_nombre']) ?></h3>
    <div class="grid">
        <?php foreach ($masDeEsteVivero as $otro): ?>
            <a href="<?= e(url('mercado/producto?id=' . (int) $otro['id'])) ?>" class="card">
                <?php if ($otro['foto_portada']): ?>
                    <img class="photo" src="<?= e(url('uploads/productos/' . basename($otro['foto_portada']))) ?>" alt="<?= e($otro['titulo']) ?>">
                <?php else: ?>
                    <div class="ph">Sin foto</div>
                <?php endif; ?>
                <div class="body">
                    <h4><?= e($otro['titulo']) ?></h4>
                    <?php if ($otro['precio'] !== null): ?>
                        <div class="price">$<?= e(number_format((float) $otro['precio'], 2)) ?> <small>/ <?= e($otro['unidad']) ?></small></div>
                    <?php else: ?>
                        <div class="hint">Precio a tratar</div>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
