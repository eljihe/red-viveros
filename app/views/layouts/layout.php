<?php
/** @var string $content */
/** @var string|null $activo */
$activo ??= '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Red de Viveros Tabasco</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="<?= e(externalUrl('fonts')) ?>">
<link rel="stylesheet" href="<?= e(url('css/app.css')) ?>">
<link rel="stylesheet" href="<?= e(externalUrl('leaflet.css')) ?>" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="<?= e(externalUrl('markercluster.css')) ?>" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="<?= e(externalUrl('markercluster.css_default')) ?>" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body>
<div class="masthead">
    <header class="top wrap">
        <div class="brand">
            <svg class="logo" viewBox="0 0 42 42" aria-hidden="true"><circle cx="21" cy="21" r="20" fill="#1E6B0F"/><path d="M21 33V17" stroke="#fff" stroke-width="2.4" stroke-linecap="round"/><path d="M21 22c-6 0-9-4-9-9 6 0 9 4 9 9z" fill="#7FD85F"/><path d="M21 19c5 0 8-3.5 8-8-5 0-8 3.5-8 8z" fill="#F2B705"/></svg>
            <div>
                <h1>Red de Viveros Tabasco</h1>
                <p>Viveros comunitarios de Sembrando Vida · venta directa, sin intermediarios</p>
            </div>
        </div>
        <div class="header-actions">
            <?php if (estaAutenticado()): ?>
                <a href="<?= e(url('cuenta')) ?>" class="btn">Hola, <?= e((string) nombreUsuario()) ?></a>
                <form method="POST" action="<?= e(url('cuenta/salir')) ?>" style="display: inline-flex;">
                    <?= csrfField() ?>
                    <button type="submit" class="btn">Cerrar sesión</button>
                </form>
            <?php else: ?>
                <a href="<?= e(url('cuenta')) ?>" class="btn">Iniciar sesión</a>
                <a href="<?= e(url('registro')) ?>" class="btn pri">Registrarme</a>
            <?php endif; ?>
        </div>
    </header>
    <div class="navband">
        <nav class="tabs wrap">
            <a href="<?= e(url('')) ?>" class="<?= $activo === 'mapa' ? 'active' : '' ?>">Mapa de viveros</a>
            <a href="<?= e(url('mercado')) ?>" class="<?= $activo === 'mercado' ? 'active' : '' ?>">Mercado</a>
            <a href="<?= e(url('cuenta')) ?>" class="<?= $activo === 'cuenta' ? 'active' : '' ?>">Mi cuenta</a>
        </nav>
    </div>
</div>
<main class="wrap">
<?= $content ?>
</main>
<footer class="site-footer">
    <div class="wrap footer-grid">
        <div class="footer-col">
            <div class="brand">
                <svg class="logo" viewBox="0 0 42 42" aria-hidden="true" width="32" height="32"><circle cx="21" cy="21" r="20" fill="#1E6B0F"/><path d="M21 33V17" stroke="#fff" stroke-width="2.4" stroke-linecap="round"/><path d="M21 22c-6 0-9-4-9-9 6 0 9 4 9 9z" fill="#7FD85F"/><path d="M21 19c5 0 8-3.5 8-8-5 0-8 3.5-8 8z" fill="#F2B705"/></svg>
                <strong>Red de Viveros Tabasco</strong>
            </div>
            <p class="footer-text">Plataforma de venta directa de plantas de los viveros comunitarios de Sembrando Vida en Tabasco, sin intermediarios.</p>
        </div>
        <div class="footer-col">
            <h4>Explorar</h4>
            <ul class="footer-links">
                <li><a href="<?= e(url('')) ?>">Mapa de viveros</a></li>
                <li><a href="<?= e(url('mercado')) ?>">Mercado</a></li>
                <li><a href="<?= e(url('registro')) ?>">Registrarme</a></li>
                <li><a href="<?= e(url('cuenta')) ?>">Mi cuenta</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Contacto</h4>
            <p class="footer-text">Comalcalco, Tabasco, México</p>
            <p class="footer-text">Piloto del programa estatal Sembrando Vida</p>
        </div>
    </div>
    <div class="footer-bottom wrap">
        <p>© 2026 Red de Viveros Tabasco · Página no oficial, no afiliada al programa de gobierno.</p>
    </div>
</footer>
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
<script src="<?= e(url('js/mapa-edicion.js')) ?>"></script>
<script src="<?= e(url('js/mapa-publico.js')) ?>"></script>
<script src="<?= e(url('js/formularios.js')) ?>"></script>
</body>
</html>
