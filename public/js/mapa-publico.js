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
    const mapsBase = (window.RV_MAPS && window.RV_MAPS.google) || 'https://www.google.com/maps';

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
    const detalleElement = document.getElementById('detalle-vivero');
    const resultadoElement = document.getElementById('resultado-filtro');

    const marcadores = new Map();
    let viveroSeleccionado = null;
    const DETALLE_INICIAL = detalleElement ? detalleElement.innerHTML : '';

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

    function mostrarDetalle(vivero) {
        if (!detalleElement) {
            return;
        }

        viveroSeleccionado = vivero.id;

        const foto = vivero.fotos.length
            ? '<img class="photo" src="' + escapeHtml(vivero.fotos[0]) + '" alt="Foto de ' + escapeHtml(vivero.nombre) + '">'
            : '<div class="ph">Sin foto</div>';

        const filas = vivero.inventario.map(function (fila) {
            const edad = fila.edad_meses ? escapeHtml(fila.edad_meses) + ' m' : '—';
            return '<tr><td>' + escapeHtml(fila.especie_nombre) + '</td><td class="num">' + escapeHtml(fila.cantidad) + '</td><td class="num">' + edad + '</td></tr>';
        }).join('');

        const tabla = vivero.inventario.length
            ? '<table class="tabla-simple"><thead><tr><th>Especie</th><th>Cantidad</th><th>Edad</th></tr></thead><tbody>' + filas + '</tbody></table>'
            : '<p class="hint">Este vivero aún no registró su inventario.</p>';

        const historia = vivero.historia ? '<p>' + escapeHtml(vivero.historia) + '</p>' : '';
        const integrantes = vivero.integrantes ? ' · ' + escapeHtml(vivero.integrantes) + ' integrantes' : '';
        const comoLlegar = mapsBase + '?q=' + encodeURIComponent(vivero.lat + ',' + vivero.lng);

        const servicios = (vivero.servicios || []).map(function (s) {
            const detalles = [s.horario, s.costo].filter(Boolean).map(escapeHtml).join(' · ');
            const descripcion = s.descripcion ? '<div class="hint">' + escapeHtml(s.descripcion) + '</div>' : '';
            return '<li><strong>' + escapeHtml(s.titulo) + '</strong>' + (detalles ? ' — ' + detalles : '') + descripcion + '</li>';
        }).join('');

        const bloqueServicios = servicios
            ? '<div><strong>Talleres y servicios</strong><ul style="margin:6px 0 0; padding-left:18px;">' + servicios + '</ul></div>'
            : '';

        detalleElement.innerHTML = foto
            + '<h3>' + escapeHtml(vivero.nombre) + '</h3>'
            + '<p class="hint">' + escapeHtml(vivero.comunidad) + ', ' + escapeHtml(vivero.municipio) + integrantes + '</p>'
            + historia
            + tabla
            + bloqueServicios
            + '<div class="actions"><a class="btn pri" href="' + comoLlegar + '" target="_blank" rel="noopener">Cómo llegar</a></div>';
    }

    function renderizarLista() {
        if (!listaElement) {
            return;
        }

        const visibles = viveros.filter(coincide);

        listaElement.innerHTML = visibles.length
            ? visibles.map(function (vivero) {
                return '<button type="button" class="vitem" data-id="' + vivero.id + '">'
                    + '<span class="nm">' + escapeHtml(vivero.nombre) + '</span>'
                    + '<span class="cm">' + escapeHtml(vivero.comunidad) + ', ' + escapeHtml(vivero.municipio) + '</span>'
                    + '<span class="pl">' + totalPlantas(vivero) + '</span>'
                    + '</button>';
            }).join('')
            : '<p class="empty">Ningún vivero coincide con el filtro.</p>';

        listaElement.querySelectorAll('.vitem').forEach(function (boton) {
            boton.addEventListener('click', function () {
                const vivero = viveros.find(function (v) {
                    return String(v.id) === boton.dataset.id;
                });
                if (!vivero) {
                    return;
                }
                mostrarDetalle(vivero);
                mapa.setView([vivero.lat, vivero.lng], 14);
                const marcador = marcadores.get(vivero.id);
                if (marcador) {
                    marcador.openPopup();
                }
            });
        });
    }

    function actualizarMarcadores() {
        grupo.clearLayers();
        marcadores.clear();

        viveros.filter(coincide).forEach(function (vivero) {
            const marcador = L.marker([vivero.lat, vivero.lng]);
            marcador.bindPopup('<b>' + escapeHtml(vivero.nombre) + '</b><br>' + escapeHtml(vivero.comunidad));
            marcador.on('click', function () {
                mostrarDetalle(vivero);
            });
            marcadores.set(vivero.id, marcador);
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

        if (detalleElement && viveroSeleccionado !== null && !visibles.some(function (v) {
            return v.id === viveroSeleccionado;
        })) {
            viveroSeleccionado = null;
            detalleElement.innerHTML = DETALLE_INICIAL;
        }
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
