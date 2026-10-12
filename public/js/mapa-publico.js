document.addEventListener('DOMContentLoaded', function () {
    const mapaElement = document.getElementById('mapa-publico');
    const datosScript = document.getElementById('datos-mapa');

    if (!mapaElement || !datosScript || typeof L === 'undefined') {
        return;
    }

    let viveros = [];
    try {
        viveros = JSON.parse(datosScript.textContent);
    } catch (error) {
        console.error('No se pudieron leer los datos del mapa', error);
        return;
    }

    const CENTRO_TABASCO = [18.2645, -93.2213];
    const tileOsm = (window.RV_TILES && window.RV_TILES.osm) || '';
    const tileEsri = (window.RV_TILES && window.RV_TILES.esri) || '';

    const mapa = L.map('mapa-publico').setView(CENTRO_TABASCO, 9);

    const capaCalles = L.tileLayer(tileOsm, {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(mapa);

    const capaSatelital = L.tileLayer(tileEsri, {
        attribution: 'Esri, Maxar, Earthstar Geographics',
        maxZoom: 19
    });

    L.control.layers({ 'Mapa': capaCalles, 'Satélite': capaSatelital }).addTo(mapa);

    const grupo = (typeof L.markerClusterGroup === 'function') ? L.markerClusterGroup() : L.layerGroup();
    mapa.addLayer(grupo);

    const filtroEspecie = document.getElementById('filtro-especie');
    const filtroMunicipio = document.getElementById('filtro-municipio');
    const listaElement = document.getElementById('lista-viveros');
    const resultadoElement = document.getElementById('resultado-filtro');

    function escapeHtml(valor) {
        const div = document.createElement('div');
        div.textContent = valor === null || valor === undefined ? '' : String(valor);
        return div.innerHTML;
    }

    function totalPlantas(vivero) {
        return vivero.inventario.reduce(function (total, fila) {
            return total + fila.cantidad;
        }, 0);
    }

    function coincide(vivero) {
        const especieId = filtroEspecie ? filtroEspecie.value : '';
        const municipioId = filtroMunicipio ? filtroMunicipio.value : '';

        if (municipioId && String(vivero.municipio_id) !== municipioId) {
            return false;
        }

        if (especieId && !vivero.inventario.some(function (fila) {
            return String(fila.especie_id) === especieId;
        })) {
            return false;
        }

        return true;
    }

    function renderizarLista() {
        if (!listaElement) {
            return;
        }

        const visibles = viveros.filter(coincide);

        listaElement.innerHTML = visibles.length
            ? visibles.map(function (vivero) {
                return '<a class="vitem" href="' + escapeHtml(vivero.url) + '">'
                    + '<span class="nm">' + escapeHtml(vivero.nombre) + '</span>'
                    + '<span class="cm">' + escapeHtml(vivero.comunidad) + ', ' + escapeHtml(vivero.municipio) + '</span>'
                    + '<span class="pl">' + totalPlantas(vivero) + '</span>'
                    + '</a>';
            }).join('')
            : '<p class="empty">Ningún vivero coincide con el filtro.</p>';
    }

    function actualizarMarcadores() {
        grupo.clearLayers();

        viveros.filter(coincide).forEach(function (vivero) {
            const marcador = L.marker([vivero.lat, vivero.lng]);
            marcador.on('click', function () {
                window.location.href = vivero.url;
            });
            grupo.addLayer(marcador);
        });
    }

    function mostrarResultadoFiltro(visibles) {
        if (!resultadoElement) {
            return;
        }

        const hayFiltroActivo = (filtroEspecie && filtroEspecie.value !== '')
            || (filtroMunicipio && filtroMunicipio.value !== '');

        if (!hayFiltroActivo) {
            resultadoElement.hidden = true;
            return;
        }

        resultadoElement.hidden = false;

        if (visibles.length === 0) {
            resultadoElement.className = 'msg err';
            resultadoElement.textContent = 'Ningún vivero coincide con tu búsqueda. Intenta con otro filtro.';
            return;
        }

        resultadoElement.className = 'msg ok';
        resultadoElement.textContent = visibles.length === 1
            ? 'Se encontró 1 vivero.'
            : 'Se encontraron ' + visibles.length + ' viveros.';
    }

    function actualizarTodo() {
        const visibles = viveros.filter(coincide);

        actualizarMarcadores();
        renderizarLista();
        mostrarResultadoFiltro(visibles);
    }

    if (filtroEspecie) {
        filtroEspecie.addEventListener('change', actualizarTodo);
    }
    if (filtroMunicipio) {
        filtroMunicipio.addEventListener('change', actualizarTodo);
    }

    actualizarTodo();

    if (viveros.length > 0) {
        const coordenadas = viveros.map(function (v) {
            return [v.lat, v.lng];
        });
        mapa.fitBounds(coordenadas, { padding: [30, 30], maxZoom: 13 });
    }
});
