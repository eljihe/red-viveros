function usarMiUbicacion(button) {
    if (!navigator.geolocation) {
        alert('Tu navegador no soporta geolocalización.');
        return;
    }

    const textoOriginal = button.textContent;
    button.disabled = true;
    button.textContent = 'Obteniendo ubicación...';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude.toFixed(4);
            const lng = position.coords.longitude.toFixed(4);
            const input = button.parentElement.querySelector('[data-gps-target]')
                || document.getElementById('coordenadas-input');

            if (input) {
                input.value = lat + ', ' + lng;
                // Avisa a otros listeners (p. ej. el mapa del panel del vivero) que el valor cambió.
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }

            if (window.mapaVivero && window.marcadorVivero) {
                const latlng = L.latLng(parseFloat(lat), parseFloat(lng));
                window.mapaVivero.setView(latlng, 12);
                window.marcadorVivero.setLatLng(latlng);
            }

            button.disabled = false;
            button.textContent = textoOriginal;
        },
        function(error) {
            alert('No se pudo obtener tu ubicación. Verifica los permisos.');
            button.disabled = false;
            button.textContent = textoOriginal;
        }
    );
}

document.addEventListener('DOMContentLoaded', function() {
    const municipioSelect = document.getElementById('municipio_select');
    const comunidadSelect = document.getElementById('comunidad_select');

    if (municipioSelect && comunidadSelect && window.comunidadesPorMunicipio) {
        function actualizarComunidades() {
            const municipioId = parseInt(municipioSelect.value);
            const comunidadIdGuardada = parseInt(comunidadSelect.dataset.savedValue || '0');
            comunidadSelect.innerHTML = '';

            if (municipioId === 0) {
                comunidadSelect.appendChild(new Option('Selecciona municipio primero', ''));
                return;
            }

            const comunidades = window.comunidadesPorMunicipio[municipioId] || [];
            if (comunidades.length === 0) {
                comunidadSelect.appendChild(new Option('No hay comunidades disponibles', ''));
                return;
            }

            comunidades.forEach(function(c) {
                const option = new Option(c.nombre, c.id);
                if (c.id === comunidadIdGuardada) {
                    option.selected = true;
                }
                comunidadSelect.appendChild(option);
            });
        }

        // Guardar valor anterior en data attribute para restaurarlo
        const comunidadIdActual = parseInt(comunidadSelect.value || '0');
        if (comunidadIdActual > 0) {
            comunidadSelect.dataset.savedValue = comunidadIdActual;
        }

        // Inicializar si hay municipio pre-seleccionado
        if (parseInt(municipioSelect.value) > 0) {
            actualizarComunidades();
        }

        municipioSelect.addEventListener('change', actualizarComunidades);
    }
});
