<?php

declare(strict_types=1);

return [
    'leaflet' => [
        'css' => 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css',
        'js' => 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js',
    ],
    'markercluster' => [
        'css' => 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css',
        'css_default' => 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css',
        'js' => 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js',
    ],
    'tiles' => [
        'osm' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        'esri_satelital' => 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
    ],
    'maps' => [
        'google' => 'https://www.google.com/maps',
    ],
    'fonts' => 'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&family=IBM+Plex+Mono:wght@400;500&display=swap',
];
