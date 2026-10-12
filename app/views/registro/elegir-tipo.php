<?php ?>
<div class="panel">
    <h2>¿Qué quieres hacer?</h2>
    <div class="roles">
        <a href="<?= e(url('registro/comprador')) ?>" class="role" data-r="comprador">
            <b>Quiero comprar</b>
            <p>Ver contacto de los viveros, buscar productos, hacer compras.</p>
        </a>
        <a href="<?= e(url('registro/vivero')) ?>" class="role" data-r="vivero">
            <b>Tengo un vivero</b>
            <p>Registrar mi vivero, inventario y productos para vender.</p>
        </a>
    </div>
    <p style="text-align: center; margin-top: 20px;">
        <a href="<?= e(url('cuenta')) ?>">¿Ya tienes cuenta? Entrar</a>
    </p>
</div>
