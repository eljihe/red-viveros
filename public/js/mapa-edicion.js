document.addEventListener('DOMContentLoaded', function() {
    if (typeof L === 'undefined') {
        console.error('Leaflet no se cargó correctamente');
        return;
    }

    const mapaElement = document.getElementById('mapa-edicion');
    const coordenadasInput = document.getElementById('coordenadas-input');

    if (!mapaElement || !coordenadasInput) {
        console.error('Elemento del mapa o input de coordenadas no encontrado');
        return;
    }

    try {
        const parts = coordenadasInput.value.trim().split(',');
        let lat = 18.2645;
        let lng = -93.2213;

        if (parts.length === 2) {
            const parsedLat = parseFloat(parts[0]);
            const parsedLng = parseFloat(parts[1]);
            if (!isNaN(parsedLat) && !isNaN(parsedLng)) {
                lat = parsedLat;
                lng = parsedLng;
            }
        }

        const mapa = L.map('mapa-edicion').setView([lat, lng], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
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

        window.mapaVivero = mapa;
        window.marcadorVivero = marcador;
        console.log('Mapa Leaflet inicializado correctamente');
    } catch (error) {
        console.error('Error al inicializar el mapa:', error);
    }
});

function usarMiUbicacion(button) {
    if (!navigator.geolocation) {
        alert('Tu navegador no soporta geolocalización.');
        return;
    }

    button.disabled = true;
    const textOriginal = button.textContent;
    button.textContent = 'Obteniendo ubicación...';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude.toFixed(4);
            const lng = position.coords.longitude.toFixed(4);
            const input = document.getElementById('coordenadas-input');
            if (input) {
                input.value = lat + ', ' + lng;
            }
            if (window.mapaVivero && window.marcadorVivero) {
                const latlng = L.latLng(parseFloat(lat), parseFloat(lng));
                window.mapaVivero.setView(latlng, 12);
                window.marcadorVivero.setLatLng(latlng);
            }
            button.disabled = false;
            button.textContent = textOriginal;
        },
        function(error) {
            alert('No se pudo obtener tu ubicación. Verifica los permisos.');
            button.disabled = false;
            button.textContent = textOriginal;
        }
    );
}
