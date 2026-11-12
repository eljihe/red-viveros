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

    const iconoVivero = L.divIcon({
        className: 'marcador-vivero',
        html: '<span>🌱</span>',
        iconSize: [34, 34],
        iconAnchor: [17, 34]
    });

    const filtroEspecie = document.getElementById('filtro-especie');
    const filtroMunicipio = document.getElementById('filtro-municipio');
    const listaElement = document.getElementById('lista-viveros');
    const resultadoElement = document.getElementById('resultado-filtro');
    const totalElement = document.getElementById('total-viveros');

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

    function resumenHistoria(texto) {
        const limpio = (texto || '').trim();
        return limpio.length > 160 ? limpio.slice(0, 157).trim() + '...' : limpio;
    }

    // Los textos suelen capturarse en MAYÚSCULAS: se muestran en formato normal.
    const PALABRAS_MENORES = ['de', 'del', 'la', 'las', 'el', 'los', 'y', 'e', 'en', 'con', 'por', 'para', 'un', 'una', 'a'];

    function esTodoMayusculas(texto) {
        return texto !== '' && texto === texto.toUpperCase() && texto !== texto.toLowerCase();
    }

    function nombreLegible(nombre) {
        const texto = String(nombre || '').trim();
        if (!esTodoMayusculas(texto)) {
            return texto;
        }
        return texto.toLowerCase().split(/\s+/).map(function (palabra, i) {
            if (i > 0 && PALABRAS_MENORES.indexOf(palabra) !== -1) {
                return palabra;
            }
            return palabra.charAt(0).toUpperCase() + palabra.slice(1);
        }).join(' ');
    }

    function textoLegible(texto) {
        const limpio = String(texto || '').trim();
        if (!esTodoMayusculas(limpio)) {
            return limpio;
        }
        const minusculas = limpio.toLowerCase();
        return minusculas.charAt(0).toUpperCase() + minusculas.slice(1);
    }

    function renderizarLista() {
        if (!listaElement) {
            return;
        }

        const visibles = viveros.filter(coincide);

        if (totalElement) {
            totalElement.textContent = visibles.length;
        }

        listaElement.innerHTML = visibles.length
            ? visibles.map(function (vivero) {
                const url = escapeHtml(vivero.url);
                const nombre = escapeHtml(nombreLegible(vivero.nombre));
                const integrantes = vivero.integrantes
                    ? '<li><i class="icon-group color-green"></i> ' + escapeHtml(vivero.integrantes) + ' integrantes</li>'
                    : '';

                return '<div class="col-md-3 col-sm-6 vivero-col">'
                    + '<div class="servive-block-in vivero-box-info">'
                    + '<h4><a href="' + url + '">' + nombre + '</a></h4>'
                    + '<div><i class="icon-leaf"></i></div>'
                    + '<ul class="list-unstyled vivero-info">'
                    + '<li><i class="icon-map-marker color-green"></i> ' + escapeHtml(vivero.comunidad) + ', ' + escapeHtml(vivero.municipio) + '</li>'
                    + '<li><i class="icon-leaf color-green"></i> ' + totalPlantas(vivero) + ' plantas</li>'
                    + integrantes
                    + '</ul>'
                    + '<a class="btn-u btn-u-sm" href="' + url + '">Leer más</a>'
                    + '</div></div>';
            }).join('')
            : '<div class="col-xs-12"><p class="text-center">Ningún vivero coincide con el filtro.</p></div>';
    }

    function actualizarMarcadores() {
        grupo.clearLayers();

        viveros.filter(coincide).forEach(function (vivero) {
            const marcador = L.marker([vivero.lat, vivero.lng], { icon: iconoVivero });
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
