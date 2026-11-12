(function () {
    var boton = document.querySelector('.navbar-toggle');
    var menu = document.querySelector('.navbar-collapse');
    if (!boton || !menu) return;
    boton.addEventListener('click', function () {
        var abierto = menu.classList.toggle('in');
        boton.setAttribute('aria-expanded', abierto ? 'true' : 'false');
    });
})();
