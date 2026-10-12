function usarMiUbicacion(button) {
    if (!navigator.geolocation) {
        alert('Tu navegador no soporta geolocalización.');
        return;
    }

    button.disabled = true;
    button.textContent = 'Obteniendo ubicación...';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude.toFixed(4);
            const lng = position.coords.longitude.toFixed(4);
            const input = button.parentElement.querySelector('[data-gps-target]');
            if (input) {
                input.value = lat + ', ' + lng;
            }
            button.disabled = false;
            button.textContent = 'Usar mi ubicación';
        },
        function(error) {
            alert('No se pudo obtener tu ubicación. Verifica los permisos.');
            button.disabled = false;
            button.textContent = 'Usar mi ubicación';
        }
    );
}

document.addEventListener('DOMContentLoaded', function() {
    const municipioSelect = document.getElementById('municipio_select');
    const comunidadSelect = document.getElementById('comunidad_select');

    if (municipioSelect && comunidadSelect && window.comunidadesPorMunicipio) {
        municipioSelect.addEventListener('change', function() {
            const municipioId = parseInt(this.value);
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
                comunidadSelect.appendChild(option);
            });
        });
    }
});
