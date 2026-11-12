(function () {
    'use strict';

    /* Filtro por categoría de "Disponibles ahora" */
    var tabs = document.querySelector('.inicio-tabs');
    if (tabs) {
        var tarjetas = document.querySelectorAll('.inicio-producto');
        tabs.addEventListener('click', function (evento) {
            var enlace = evento.target.closest('a[data-categoria]');
            if (!enlace) {
                return;
            }
            evento.preventDefault();
            tabs.querySelectorAll('li').forEach(function (li) {
                li.classList.toggle('active', li === enlace.parentNode);
            });
            var categoria = enlace.getAttribute('data-categoria');
            tarjetas.forEach(function (tarjeta) {
                tarjeta.hidden = categoria !== '' && tarjeta.getAttribute('data-categoria') !== categoria;
            });
        });
    }

    /* Slider de portada (reemplaza jquery.cslider del template; usa sus mismas clases CSS) */
    var slider = document.getElementById('da-slider');
    if (!slider) {
        return;
    }
    var slides = slider.querySelectorAll('.da-slide');
    if (slides.length === 0) {
        return;
    }
    var animaciones = ['da-slide-fromright', 'da-slide-fromleft', 'da-slide-toright', 'da-slide-toleft'];
    var actual = 0;
    var temporizador = null;

    function limpiar(slide) {
        animaciones.forEach(function (clase) { slide.classList.remove(clase); });
    }

    function mostrar(indice, haciaAdelante) {
        if (indice === actual) {
            return;
        }
        var saliente = slides[actual];
        var entrante = slides[indice];
        limpiar(saliente);
        limpiar(entrante);
        saliente.classList.remove('da-slide-current');
        saliente.classList.add(haciaAdelante ? 'da-slide-toleft' : 'da-slide-toright');
        entrante.classList.add('da-slide-current', haciaAdelante ? 'da-slide-fromright' : 'da-slide-fromleft');
        actual = indice;
    }

    function siguiente() { mostrar((actual + 1) % slides.length, true); }
    function anterior() { mostrar((actual - 1 + slides.length) % slides.length, false); }

    function iniciar() {
        if (slides.length > 1 && temporizador === null) {
            temporizador = window.setInterval(siguiente, 6000);
        }
    }
    function detener() {
        if (temporizador !== null) {
            window.clearInterval(temporizador);
            temporizador = null;
        }
    }

    slides[0].classList.add('da-slide-current');

    var prev = slider.querySelector('.da-arrows-prev');
    var next = slider.querySelector('.da-arrows-next');
    if (prev) { prev.addEventListener('click', anterior); }
    if (next) { next.addEventListener('click', siguiente); }
    slider.addEventListener('mouseenter', detener);
    slider.addEventListener('mouseleave', iniciar);
    iniciar();
})();
