<?php
/** @var array<int, array<string, mixed>> $productos */
/** @var array<int, string> $fotosPortada */

$categorias = array_values(array_unique(array_map(static fn (array $p): string => (string) $p['categoria'], $productos)));

$ofrecemos = [
    ['icon-leaf', 'Cacao', 'Plántulas e injertos'],
    ['icon-tree-deciduous', 'Maderables', 'Cedro, caoba y macuilis'],
    ['icon-apple', 'Frutales', 'Cítricos, mango y guanábana'],
    ['icon-flag', 'Plátano', 'Hijuelos seleccionados'],
    ['icon-sun', 'Coco y palmas', 'Plantas de vivero'],
    ['icon-tint', 'Abono orgánico', 'Composta y lombricomposta'],
];
?>
<!--=== Slider ===-->
<?php
$slidesPortada = [
    [
        'titulo' => ['Del vivero a tu parcela,', 'sin intermediarios'],
        'texto' => ['Plantas y productos de los viveros comunitarios', 'de Sembrando Vida, directo de quienes los siembran.'],
        'enlace' => ['viveros', 'Ver el mapa de viveros'],
    ],
    [
        'titulo' => ['Compra directo', 'al que siembra'],
        'texto' => ['Encuentra plantas, frutos y abono', 'y escríbele al vivero por WhatsApp.'],
        'enlace' => ['mercado', 'Explorar productos'],
    ],
    [
        'titulo' => ['Una red de viveros', 'hecha por su gente'],
        'texto' => ['Regístrate gratis con tu teléfono', 'y conoce a quien produce tus plantas.'],
        'enlace' => ['registro', 'Crear mi cuenta'],
    ],
];
?>
<div class="slider-inner">
    <div id="da-slider" class="da-slider">
        <?php foreach ($slidesPortada as $i => $s): ?>
            <div class="da-slide">
                <h2><?= implode(' <br /> ', array_map(static fn (string $l): string => '<i>' . e($l) . '</i>', $s['titulo'])) ?></h2>
                <p><?= implode(' <br /> ', array_map(static fn (string $l): string => '<i>' . e($l) . '</i>', $s['texto'])) ?></p>
                <a class="da-link" href="<?= e(url($s['enlace'][0])) ?>"><?= e($s['enlace'][1]) ?></a>
                <?php if ($fotosPortada !== []): ?>
                    <div class="da-img"><img src="<?= e($fotosPortada[$i % count($fotosPortada)]) ?>" alt=""></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <div class="da-arrows">
            <span class="da-arrows-prev"></span>
            <span class="da-arrows-next"></span>
        </div>
    </div><!--/da-slider-->
</div><!--/slider-->
<!--=== End Slider ===-->

<!--=== Descripción ===-->
<div class="container">
    <!--Title Box-->
    <div class="title-box">
        <div class="title-box-text">Una red de viveros <span class="color-green">hecha</span> por su gente</div>
        <p>Creemos que la tierra sana, las plantas sanas y las comunidades unidas van de la mano. Red de Viveros reúne a los viveros comunitarios de Sembrando Vida en Comalcalco para que compres directamente a quienes siembran, cuidan y hacen crecer cada planta. Sin intermediarios y con precios justos: sabes quién produjo lo que compras, de dónde viene y cómo llegar a él.</p>
    </div>
    <!--End Title Box-->
</div>
<!--=== End Descripción ===-->

<div class="container">

    <!-- Cómo comprar -->
    <div class="row margin-bottom-40">
        <div class="col-md-7">
            <div class="headline"><h2>Cómo comprar</h2></div>
            <div class="service">
                <i class="icon-user service-icon"></i>
                <div class="desc">
                    <h4>1. Regístrate</h4>
                    <p>Crea tu cuenta con tu número de teléfono y un PIN de 6 dígitos. Es rápido y gratis.</p>
                </div>
            </div>
            <div class="service">
                <i class="icon-search service-icon"></i>
                <div class="desc">
                    <h4>2. Busca tu producto</h4>
                    <p>Explora el mercado o el mapa y encuentra el vivero más cercano con lo que necesitas.</p>
                </div>
            </div>
            <div class="service">
                <i class="icon-phone service-icon"></i>
                <div class="desc">
                    <h4>3. Contacta al agricultor por WhatsApp</h4>
                    <p>Escríbele directo y acuerden precio, cantidad y entrega.</p>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="headline"><h2>Compra directo al que siembra</h2></div>
            <p>No necesitas ser experto en tecnología. Todo está pensado para que sea tan fácil como enviar un mensaje.</p>
            <ul class="list-unstyled margin-bottom-20">
                <li><i class="icon-ok color-green"></i> Registro gratuito, sin comisiones</li>
                <li><i class="icon-ok color-green"></i> Trato directo con el productor por WhatsApp</li>
                <li><i class="icon-ok color-green"></i> Viveros verificados por la red</li>
            </ul>
            <a class="btn-u" href="<?= e(url('registro')) ?>">Crear mi cuenta</a>
        </div>
    </div><!--/row-->

    <!-- Disponibles ahora -->
    <div class="headline"><h2>Disponibles ahora</h2></div>
    <?php if (empty($productos)): ?>
        <p>Todavía no hay productos publicados.</p>
    <?php else: ?>
        <ul class="nav nav-tabs inicio-tabs margin-bottom-20">
            <li class="active"><a href="#" data-categoria="">Todos</a></li>
            <?php foreach ($categorias as $cat): ?>
                <li><a href="#" data-categoria="<?= e($cat) ?>"><?= e($cat) ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="row inicio-productos">
            <?php foreach ($productos as $p): ?>
                <div class="col-md-3 col-sm-6 inicio-producto" data-categoria="<?= e((string) $p['categoria']) ?>">
                    <div class="thumbnails thumbnail-style thumbnail-kenburn">
                        <div class="thumbnail-img">
                            <div class="overflow-hidden">
                                <?php if ($p['foto_portada']): ?>
                                    <img class="img-responsive" src="<?= e(url('uploads/productos/' . basename((string) $p['foto_portada']))) ?>" alt="<?= e((string) $p['titulo']) ?>" loading="lazy">
                                <?php else: ?>
                                    <span class="sin-foto">Sin foto</span>
                                <?php endif; ?>
                            </div>
                            <a class="btn-more hover-effect" href="<?= e(url('mercado/producto?id=' . (int) $p['id'])) ?>">ver +</a>
                        </div>
                        <div class="caption">
                            <h3><a class="hover-effect" href="<?= e(url('mercado/producto?id=' . (int) $p['id'])) ?>"><?= e((string) $p['titulo']) ?></a></h3>
                            <p>
                                <?php if ($p['precio'] !== null): ?>
                                    <strong class="color-green">$<?= e(number_format((float) $p['precio'], 2)) ?> / <?= e((string) $p['unidad']) ?></strong>
                                <?php else: ?>
                                    <strong class="color-green">Precio a tratar</strong>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div><!--/row-->
    <?php endif; ?>
    <p class="text-center margin-bottom-40"><a class="btn-u" href="<?= e(url('mercado')) ?>">Ver todos los productos</a></p>

    <!-- Lo que ofrecemos -->
    <div class="headline"><h2>Lo que ofrecemos</h2></div>
    <div class="row margin-bottom-40">
        <?php foreach ($ofrecemos as [$icono, $nombre, $detalle]): ?>
            <div class="col-md-4 col-sm-6">
                <div class="service">
                    <i class="<?= e($icono) ?> service-icon"></i>
                    <div class="desc">
                        <h4><?= e($nombre) ?></h4>
                        <p><?= e($detalle) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div><!--/row-->
</div><!--/container-->
