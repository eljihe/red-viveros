<?php
/** @var array<string, mixed> $producto */
/** @var array<int, array<string, mixed>> $fotos */
/** @var array<int, array<string, mixed>> $masDeEsteVivero */
/** @var float|null $distanciaKm */
/** @var bool|null $llegaAZona */
/** @var array{estado: string, mensaje?: string} $contacto */

$comoLlegar = externalUrl('maps.google') . '?q=' . $producto['lat'] . ',' . $producto['lng'];
?>
<!--=== Breadcrumbs ===-->
<div class="breadcrumbs margin-bottom-40">
    <div class="container">
        <h1 class="pull-left">Mercado</h1>
        <ul class="pull-right breadcrumb">
            <li><a href="<?= e(url('')) ?>">Inicio</a></li>
            <li><a href="<?= e(url('mercado')) ?>">Mercado</a></li>
            <li class="active"><?= e($producto['titulo']) ?></li>
        </ul>
    </div><!--/container-->
</div><!--/breadcrumbs-->
<!--=== End Breadcrumbs ===-->

<!--=== Content Part ===-->
<div class="container">
    <div class="row portfolio-item margin-bottom-50">
        <!-- Carousel -->
        <div class="col-md-7">
            <?php if (!empty($fotos)): ?>
                <div class="carousel slide carousel-v1" id="myCarousel">
                    <div class="carousel-inner">
                        <?php foreach ($fotos as $i => $foto): ?>
                            <div class="item<?= $i === 0 ? ' active' : '' ?>">
                                <img alt="<?= e($producto['titulo']) ?>" src="<?= e(url('uploads/productos/' . basename($foto['archivo']))) ?>"<?= $i > 0 ? ' loading="lazy"' : '' ?>>
                                <div class="carousel-caption">
                                    <p><?= e($producto['titulo']) ?><?= count($fotos) > 1 ? ' · ' . ($i + 1) . ' de ' . count($fotos) : '' ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($fotos) > 1): ?>
                        <div class="carousel-arrow">
                            <a data-slide="prev" href="#myCarousel" class="left carousel-control" aria-label="Foto anterior">
                                <i class="icon-angle-left"></i>
                            </a>
                            <a data-slide="next" href="#myCarousel" class="right carousel-control" aria-label="Foto siguiente">
                                <i class="icon-angle-right"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="tag-box tag-box-v2 text-center"><p>Sin foto</p></div>
            <?php endif; ?>
        </div>
        <!-- End Carousel -->

        <!-- Content Info -->
        <div class="col-md-5">
            <h2><?= e($producto['titulo']) ?></h2>
            <?php if ($producto['precio'] !== null): ?>
                <p><strong class="color-green">$<?= e(number_format((float) $producto['precio'], 2)) ?></strong> por <?= e($producto['unidad']) ?></p>
            <?php else: ?>
                <p>Precio a tratar con el vivero</p>
            <?php endif; ?>
            <?php if ($producto['descripcion']): ?>
                <p><?= e($producto['descripcion']) ?></p>
            <?php endif; ?>
            <ul class="list-unstyled">
                <li><i class="icon-home color-green"></i> <?= e($producto['vivero_nombre']) ?></li>
                <li><i class="icon-map-marker color-green"></i> <?= e($producto['comunidad']) ?>, <?= e($producto['municipio']) ?></li>
                <li><i class="icon-tags color-green"></i> <?= e($producto['categoria']) ?></li>
                <?php if ($producto['disponible'] !== null): ?>
                    <li><i class="icon-archive color-green"></i> Disponible: <?= e((string) $producto['disponible']) ?> <?= e((int) $producto['disponible'] === 1 ? $producto['unidad'] : $producto['unidad_plural']) ?></li>
                <?php endif; ?>
                <li><i class="icon-truck color-green"></i>
                    <?php if ((int) $producto['hace_entrega'] === 1): ?>
                        Entrega hasta <?= e((string) $producto['entrega_km']) ?> km<?php if ($producto['entrega_costo']): ?> · <?= e($producto['entrega_costo']) ?><?php endif; ?>
                    <?php else: ?>
                        Se recoge en el vivero
                    <?php endif; ?>
                </li>
            </ul>
            <p><a class="btn-u btn-u-large" href="<?= e($comoLlegar) ?>" target="_blank" rel="noopener">CÓMO LLEGAR</a></p>
        </div>
        <!-- End Content Info -->
    </div><!--/row-->

    <div class="tag-box tag-box-v2">
        <?php if ($contacto['estado'] === 'sin_sesion'): ?>
            <p class="registro-aviso"><strong>Para ver el contacto necesita estar registrado.</strong> El teléfono y WhatsApp solo se muestran a viveros y compradores registrados. Registrarse es gratis.</p>
            <p><a href="<?= e(url('registro')) ?>" class="btn-u btn-u-lg">REGISTRARME</a></p>
        <?php elseif ($contacto['estado'] === 'limite'): ?>
            <p><strong>Contacto.</strong> <?= e($contacto['mensaje'] ?? '') ?></p>
        <?php else: ?>
            <p><strong>Contacto:</strong> <span class="telefono"><?= e($producto['vivero_telefono']) ?></span></p>
            <p><a class="btn-u btn-whatsapp" href="<?= e(externalUrl('whatsapp') . '52' . $producto['vivero_telefono'] . '?text=' . rawurlencode('Hola, me interesa "' . $producto['titulo'] . '" que publicaron en la Red de Viveros Tabasco.')) ?>" target="_blank" rel="noopener">ESCRIBIR POR WHATSAPP</a></p>
        <?php endif; ?>
        <?php if ($distanciaKm !== null): ?>
            <p>
                Estás a <?= $distanciaKm < 1 ? 'menos de 1' : e((string) round($distanciaKm)) ?> km de este vivero
                <?php if ($llegaAZona === true): ?> · <strong class="color-green">Llega a tu zona</strong>
                <?php elseif ($llegaAZona === false): ?> · no llega a tu zona
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="margin-bottom-20 clearfix"></div>

    <?php if (!empty($masDeEsteVivero)): ?>
        <!-- Recent Works -->
        <div class="headline"><h2>Más de <?= e($producto['vivero_nombre']) ?></h2></div>
        <div class="margin-bottom-40">
            <ul class="row list-unstyled recent-work">
                <?php foreach ($masDeEsteVivero as $otro): ?>
                    <li class="col-md-3 col-sm-6 md-margin-bottom-20">
                        <a href="<?= e(url('mercado/producto?id=' . (int) $otro['id'])) ?>">
                            <em class="overflow-hidden">
                                <?php if ($otro['foto_portada']): ?>
                                    <img src="<?= e(url('uploads/productos/' . basename($otro['foto_portada']))) ?>" alt="<?= e($otro['titulo']) ?>" loading="lazy">
                                <?php else: ?>
                                    <span class="sin-foto">Sin foto</span>
                                <?php endif; ?>
                            </em>
                            <span>
                                <strong><?= e($otro['titulo']) ?></strong>
                                <i><?= $otro['precio'] !== null ? '$' . e(number_format((float) $otro['precio'], 2)) . ' / ' . e($otro['unidad']) : 'Precio a tratar' ?></i>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div><!--/row-->
        <!-- End Recent Works -->
    <?php endif; ?>
</div><!--/container-->
<!--=== End Content Part ===-->
