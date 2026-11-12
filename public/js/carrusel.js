(function () {
    document.querySelectorAll('.carousel').forEach(function (carrusel) {
        var items = carrusel.querySelectorAll('.carousel-inner > .item');
        if (items.length < 2) return;
        var actual = 0;
        items.forEach(function (item, i) { if (item.classList.contains('active')) actual = i; });

        function mostrar(indice) {
            items[actual].classList.remove('active');
            actual = (indice + items.length) % items.length;
            items[actual].classList.add('active');
        }

        carrusel.querySelectorAll('.carousel-control').forEach(function (control) {
            control.addEventListener('click', function (evento) {
                evento.preventDefault();
                mostrar(actual + (control.dataset.slide === 'prev' ? -1 : 1));
            });
        });
    });
})();
