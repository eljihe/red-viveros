function parseCoordenadasTexto(texto) {
    texto = (texto || '').trim();

    const patronDms = /(\d{1,3})\s*[°º]\s*(\d{1,2})\s*['′]\s*(\d{1,2}(?:\.\d+)?)\s*["″]?\s*([NSEWnsew])/gu;
    const coincidencias = [...texto.matchAll(patronDms)];

    if (coincidencias.length === 2) {
        const valores = {};
        coincidencias.forEach(function (m) {
            const grados = parseFloat(m[1]);
            const minutos = parseFloat(m[2]);
            const segundos = parseFloat(m[3]);
            const direccion = m[4].toUpperCase();
            let decimal = grados + (minutos / 60) + (segundos / 3600);
            if (direccion === 'S' || direccion === 'W') {
                decimal *= -1;
            }
            valores[direccion] = decimal;
        });

        const lat = valores.N !== undefined ? valores.N : valores.S;
        const lng = valores.E !== undefined ? valores.E : valores.W;
        if (lat !== undefined && lng !== undefined) {
            return [lat, lng];
        }
    }

    const partes = texto.split(',');
    if (partes.length === 2) {
        const lat = parseFloat(partes[0]);
        const lng = parseFloat(partes[1]);
        if (!isNaN(lat) && !isNaN(lng)) {
            return [lat, lng];
        }
    }

    return [null, null];
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof L === 'undefined') {
        console.error('Leaflet no se cargó correctamente');
        return;
    }

    const mapaElement = document.getElementById('mapa-edicion');
    const coordenadasInput = document.getElementById('coordenadas-input');

    if (!mapaElement || !coordenadasInput) {
        return;
    }

    try {
        let [lat, lng] = parseCoordenadasTexto(coordenadasInput.value);
        if (lat === null || lng === null) {
            lat = 18.2645;
            lng = -93.2213;
        }

        const mapa = L.map('mapa-edicion').setView([lat, lng], 12);

        const tileOsm = (window.RV_TILES && window.RV_TILES.osm) || 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        L.tileLayer(tileOsm, {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(mapa);

        const marcador = L.marker([lat, lng], { draggable: true }).addTo(mapa);

        function actualizarCoordenadas(latlng) {
            const latVal = latlng.lat.toFixed(4);
            const lngVal = latlng.lng.toFixed(4);
            coordenadasInput.value = latVal + ', ' + lngVal;
        }

        marcador.on('dragend', function(e) {
            actualizarCoordenadas(e.target.getLatLng());
        });

        mapa.on('click', function(e) {
            marcador.setLatLng(e.latlng);
            actualizarCoordenadas(e.latlng);
        });

        // Si el usuario edita el campo a mano (decimal o grados/minutos/segundos),
        // el marcador se mueve también, para que quede claro que sí se registró el cambio.
        coordenadasInput.addEventListener('input', function() {
            const [latTexto, lngTexto] = parseCoordenadasTexto(coordenadasInput.value);
            if (latTexto !== null && lngTexto !== null) {
                const latlng = L.latLng(latTexto, lngTexto);
                marcador.setLatLng(latlng);
                mapa.setView(latlng, mapa.getZoom());
            }
        });

        window.mapaVivero = mapa;
        window.marcadorVivero = marcador;
    } catch (error) {
        console.error('Error al inicializar el mapa:', error);
    }
});
