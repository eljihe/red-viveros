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
?>
<!--=== Breadcrumbs ===-->
<div class="breadcrumbs margin-bottom-40">
    <div class="container">
        <h1 class="pull-left">Vivero</h1>
        <ul class="pull-right breadcrumb">
            <li><a href="<?= e(url('')) ?>">Inicio</a></li>
            <li><a href="<?= e(url('viveros')) ?>">Viveros</a></li>
            <li class="active"><?= e($vivero['nombre']) ?></li>
        </ul>
    </div><!--/container-->
</div><!--/breadcrumbs-->
<!--=== End Breadcrumbs ===-->

<!--=== Content Part ===-->
<div class="container">
    <div class="row portfolio-item margin-bottom-50">
        <!-- Content Info -->
        <div class="col-md-6 md-margin-bottom-40">
            <h2><?= e($vivero['nombre']) ?></h2>
            <?php if ($vivero['historia']): ?>
                <p><?= e($vivero['historia']) ?></p>
            <?php else: ?>
                <p>Vivero comunitario de <?= e($vivero['comunidad']) ?>, <?= e($vivero['municipio']) ?>.</p>
            <?php endif; ?>
            <div class="row portfolio-item1">
                <div class="col-xs-6">
                    <ul class="list-unstyled">
                        <li><i class="icon-map-marker color-green"></i> <?= e($vivero['comunidad']) ?>, <?= e($vivero['municipio']) ?></li>
                        <?php if ($vivero['integrantes']): ?>
                            <li><i class="icon-group color-green"></i> <?= e((string) $vivero['integrantes']) ?> integrantes</li>
                        <?php endif; ?>
                        <li><i class="icon-leaf color-green"></i> <?= e((string) $totalPlantas) ?> plantas</li>
                        <li><i class="icon-tags color-green"></i> <?= e((string) count($vivero['inventario'])) ?> <?= count($vivero['inventario']) === 1 ? 'especie' : 'especies' ?></li>
                    </ul>
                </div>
                <div class="col-xs-6">
                    <ul class="list-unstyled">
                        <li><i class="icon-ok-sign color-green"></i> Vivero registrado</li>
                        <?php if ($ofreceEntregas): ?>
                            <li><i class="icon-truck color-green"></i> Ofrece entregas</li>
                        <?php endif; ?>
                        <?php if (!empty($vivero['servicios'])): ?><li><i class="icon-wrench color-green"></i> <?= e((string) count($vivero['servicios'])) ?> talleres y servicios</li><?php endif; ?>
                        <?php if (!empty($productos)): ?><li><i class="icon-shopping-cart color-green"></i> <?= e((string) count($productos)) ?> productos</li><?php endif; ?>
                    </ul>
                </div>
            </div>
            <p><a class="btn-u btn-u-large" href="<?= e($comoLlegar) ?>" target="_blank" rel="noopener">CÓMO LLEGAR</a></p>
        </div>
        <!-- End Content Info -->

        <!-- Carousel -->
        <div class="col-md-6">
            <?php if (!empty($fotos)): ?>
                <div class="carousel slide carousel-v1" id="myCarousel">
                    <div class="carousel-inner">
                        <?php foreach ($fotos as $i => $foto): ?>
                            <div class="item<?= $i === 0 ? ' active' : '' ?>">
                                <img alt="Foto de <?= e($vivero['nombre']) ?>" src="<?= e(url('uploads/viveros/' . basename($foto))) ?>"<?= $i > 0 ? ' loading="lazy"' : '' ?>>
                                <div class="carousel-caption">
                                    <p><?= e($vivero['comunidad']) ?>, <?= e($vivero['municipio']) ?><?= count($fotos) > 1 ? ' · ' . ($i + 1) . ' de ' . count($fotos) : '' ?></p>
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
    </div><!--/row-->

    <!-- Contacto -->
    <div class="tag-box tag-box-v2">
        <?php if ($telefonoVisible): ?>
            <p><strong>Contacto:</strong> <span class="telefono"><?= e($vivero['telefono']) ?></span></p>
            <p><a class="btn-u btn-whatsapp" href="<?= e(externalUrl('whatsapp') . '52' . $vivero['telefono'] . '?text=' . rawurlencode('Hola, vi el vivero "' . $vivero['nombre'] . '" en la Red de Viveros Tabasco y quisiera más información sobre sus plantas.')) ?>" target="_blank" rel="noopener">ESCRIBIR POR WHATSAPP</a></p>
        <?php else: ?>
            <p><strong>Para ver el contacto necesita estar registrado.</strong> El teléfono y WhatsApp solo se muestran a usuarios registrados. Registrarse es gratis.</p>
            <p><a href="<?= e(url('registro')) ?>" class="btn-u">REGISTRARME</a></p>
        <?php endif; ?>
        <p>Ubicación: <span class="telefono"><?= e((string) $vivero['lat']) ?>, <?= e((string) $vivero['lng']) ?></span></p>
    </div>

    <div class="margin-bottom-20 clearfix"></div>

    <!-- Talleres y servicios -->
    <?php if (!empty($vivero['servicios'])): ?>
        <div class="headline"><h2>Talleres y servicios</h2></div>
        <div class="row servive-block margin-bottom-20">
            <?php foreach ($vivero['servicios'] as $s): ?>
                <div class="col-md-4 md-margin-bottom-20">
                    <div class="servive-block-in">
                        <i class="icon-wrench"></i>
                        <h4><?= e($s['titulo']) ?></h4>
                        <?php $detalles = array_filter([$s['horario'], $s['costo']]); ?>
                        <?php if ($detalles): ?><p><strong><?= e(implode(' · ', $detalles)) ?></strong></p><?php endif; ?>
                        <?php if ($s['descripcion']): ?><p><?= e($s['descripcion']) ?></p><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="margin-bottom-40"><em>Los servicios y condiciones deben confirmarse directamente con el vivero.</em></p>
    <?php endif; ?>

    <!-- Productos de este vivero (Recent Works) -->
    <?php if (!empty($productos)): ?>
        <div class="headline"><h2>Productos de este vivero</h2></div>
        <div class="margin-bottom-40">
            <ul class="row list-unstyled recent-work">
                <?php foreach ($productos as $p): ?>
                    <li class="col-md-3 col-sm-6 md-margin-bottom-20">
                        <a href="<?= e(url('mercado/producto?id=' . (int) $p['id'])) ?>">
                            <em class="overflow-hidden">
                                <?php if ($p['foto_portada']): ?>
                                    <img src="<?= e(url('uploads/productos/' . basename($p['foto_portada']))) ?>" alt="<?= e($p['titulo']) ?>" loading="lazy">
                                <?php else: ?>
                                    <span class="sin-foto">Sin foto</span>
                                <?php endif; ?>
                            </em>
                            <span>
                                <strong><?= e($p['titulo']) ?></strong>
                                <i><?= $p['precio'] !== null ? '$' . e(number_format((float) $p['precio'], 2)) . ' / ' . e($p['unidad']) : 'Precio a tratar' ?></i>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <p class="text-center margin-bottom-40"><em>Antes de trasladarte, confirma existencias, precios y horario con el responsable.</em></p>
</div><!--/container-->
<!--=== End Content Part ===-->
