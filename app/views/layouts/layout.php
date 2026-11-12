<?php
/** @var string $content */
/** @var string|null $activo */
/** @var bool|null $plantilla */
$activo ??= '';
$plantilla ??= false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Red de Viveros Tabasco</title>
<link rel="icon" type="image/png" href="<?= e(url('assets/img/favicon.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="<?= e(externalUrl('fonts')) ?>">
<!-- CSS del template (Unify) -->
<link rel="stylesheet" href="<?= e(url('assets/plugins/bootstrap/css/bootstrap.min.css')) ?>">
<link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>">
<link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
<link rel="stylesheet" href="<?= e(url('assets/css/headers/header1.css')) ?>">
<link rel="stylesheet" href="<?= e(url('assets/css/responsive.css')) ?>">
<link rel="stylesheet" href="<?= e(url('assets/plugins/font-awesome/css/font-awesome.min.css')) ?>">
<link rel="stylesheet" href="<?= e(url('assets/css/pages/portfolio-v2.css')) ?>">
<link rel="stylesheet" href="<?= e(url('assets/css/pages/page_search.css')) ?>">
<link rel="stylesheet" href="<?= e(url('assets/css/pages/page_magazine.css')) ?>">
<link rel="stylesheet" href="<?= e(url('assets/css/pages/page_log_reg_v1.css')) ?>">
<link rel="stylesheet" href="<?= e(url('assets/css/themes/default.css')) ?>">
<link rel="stylesheet" href="<?= e(url('assets/plugins/parallax-slider/css/parallax-slider.css')) ?>">
<!-- CSS del proyecto -->
<link rel="stylesheet" href="<?= e(assetUrl('css/app.css')) ?>">
<link rel="stylesheet" href="<?= e(assetUrl('css/proyecto.css')) ?>">
<link rel="stylesheet" href="<?= e(externalUrl('leaflet.css')) ?>" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="<?= e(externalUrl('markercluster.css')) ?>" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="<?= e(externalUrl('markercluster.css_default')) ?>" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body>
<!--=== Top ===-->
<div class="top">
    <div class="container">
        <ul class="loginbar pull-right">
            <?php if (estaAutenticado()): ?>
                <li><a href="<?= e(url('cuenta')) ?>">Hola, <?= e((string) nombreUsuario()) ?></a></li>
                <li class="devider"></li>
                <li>
                    <form method="POST" action="<?= e(url('cuenta/salir')) ?>" class="loginbar-form">
                        <?= csrfField() ?>
                        <button type="submit">Cerrar sesión</button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="<?= e(url('cuenta')) ?>">Iniciar sesión</a></li>
                <li class="devider"></li>
                <li><a href="<?= e(url('registro')) ?>">Registrarme</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div><!--/top-->

<!--=== Header ===-->
<div class="header">
    <div class="navbar navbar-default" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" aria-label="Abrir menú" aria-expanded="false">
                    <span class="sr-only">Abrir menú</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?= e(url('')) ?>">
                    <img class="logo-vivero" src="<?= e(url('assets/img/logo-vetaagro.png')) ?>" alt="VetaAgro, red de productores campesinos">
                </a>
            </div>
            <div class="collapse navbar-collapse navbar-responsive-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class="<?= $activo === 'inicio' ? 'active' : '' ?>"><a href="<?= e(url('')) ?>">Inicio</a></li>
                    <li class="<?= $activo === 'viveros' ? 'active' : '' ?>"><a href="<?= e(url('viveros')) ?>">Viveros</a></li>
                    <li class="<?= $activo === 'mercado' ? 'active' : '' ?>"><a href="<?= e(url('mercado')) ?>">Mercado</a></li>
                    <li class="<?= $activo === 'cuenta' ? 'active' : '' ?>"><a href="<?= e(url('cuenta')) ?>">Mi cuenta</a></li>
                </ul>
            </div><!--/navbar-collapse-->
        </div>
    </div>
</div><!--/header-->

<?php if ($plantilla): ?>
<?= $content ?>
<?php else: ?>
<div class="container contenido-legacy">
<?= $content ?>
</div>
<?php endif; ?>

<!--=== Footer ===-->
<div class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 md-margin-bottom-40">
                <div class="headline"><h2>Acerca de</h2></div>
                <p>Plataforma de venta directa de plantas de los viveros comunitarios de Sembrando Vida en Tabasco, sin intermediarios.</p>
            </div><!--/col-md-4-->
            <div class="col-md-4 md-margin-bottom-40">
                <div class="headline"><h2>Explorar</h2></div>
                <ul class="list-unstyled">
                    <li><a href="<?= e(url('viveros')) ?>">Viveros</a></li>
                    <li><a href="<?= e(url('mercado')) ?>">Mercado</a></li>
                    <li><a href="<?= e(url('registro')) ?>">Registrarme</a></li>
                    <li><a href="<?= e(url('cuenta')) ?>">Mi cuenta</a></li>
                </ul>
            </div><!--/col-md-4-->
            <div class="col-md-4">
                <div class="headline"><h2>Contacto</h2></div>
                <address class="md-margin-bottom-40">
                    Comalcalco, Tabasco, México<br>
                    Piloto del programa estatal Sembrando Vida
                </address>
            </div><!--/col-md-4-->
        </div><!--/row-->
    </div><!--/container-->
</div><!--/footer-->

<!--=== Copyright ===-->
<div class="copyright">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <p class="copyright-space">© 2026 Red de Viveros Tabasco · Página no oficial, no afiliada al programa de gobierno.</p>
            </div>
        </div><!--/row-->
    </div><!--/container-->
</div><!--/copyright-->

<script src="<?= e(externalUrl('leaflet.js')) ?>" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="<?= e(externalUrl('markercluster.js')) ?>" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
window.RV_TILES = {
    osm: <?= json_encode(externalUrl('tiles.osm')) ?>,
    esri: <?= json_encode(externalUrl('tiles.esri_satelital')) ?>
};
window.RV_MAPS = {
    google: <?= json_encode(externalUrl('maps.google')) ?>
};
</script>
<script src="<?= e(assetUrl('js/mapa-edicion.js')) ?>"></script>
<script src="<?= e(assetUrl('js/mapa-publico.js')) ?>"></script>
<script src="<?= e(assetUrl('js/formularios.js')) ?>"></script>
<script src="<?= e(assetUrl('js/menu.js')) ?>"></script>
<script src="<?= e(assetUrl('js/carrusel.js')) ?>"></script>
<script src="<?= e(assetUrl('js/inicio.js')) ?>"></script>
</body>
</html>
