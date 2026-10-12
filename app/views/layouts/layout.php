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
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&family=IBM+Plex+Mono:wght@400;500&display=swap">
<link rel="stylesheet" href="<?= e(url('css/app.css')) ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" integrity="sha512-h9FcoyWjrSciJTVLiS1IVfIo6F8v1cneYwKlztwNeuUlwbuQYwxkY7tNVbS2iNZWC2RW0nCjbqQE3xTvAkwVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
<footer class="wrap">
    <p>Red de Viveros Tabasco · Sembrando Vida</p>
</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js" integrity="sha512-WXoSL2lrKOSIDIcoxaxEP+sNsjlrBwenJscnsvghnsmZosbZW+76msjE60ai/qXt46Mhx+ow7Zeg+hVPMgm/2HA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="<?= e(url('js/mapa-edicion.js')) ?>"></script>
<script src="<?= e(url('js/formularios.js')) ?>"></script>
</body>
</html>
