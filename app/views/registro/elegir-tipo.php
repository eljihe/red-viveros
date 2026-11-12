<!--=== Breadcrumbs ===-->
<div class="breadcrumbs margin-bottom-40">
    <div class="container">
        <h1 class="pull-left">Registrarme</h1>
        <ul class="pull-right breadcrumb">
            <li><a href="<?= e(url('')) ?>">Inicio</a></li>
            <li class="active">Registrarme</li>
        </ul>
    </div><!--/container-->
</div><!--/breadcrumbs-->
<!--=== End Breadcrumbs ===-->

<!--=== Content Part ===-->
<div class="container">
    <div class="headline"><h2>¿Qué quieres hacer?</h2></div>

    <div class="row servive-block margin-bottom-40">
        <div class="col-md-6 md-margin-bottom-20">
            <div class="servive-block-in">
                <i class="icon-shopping-cart"></i>
                <h4><a href="<?= e(url('registro/comprador')) ?>">Quiero comprar</a></h4>
                <p>Ver contacto de los viveros, buscar productos, hacer compras.</p>
                <p><a href="<?= e(url('registro/comprador')) ?>" class="btn-u">REGISTRARME COMO COMPRADOR</a></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="servive-block-in">
                <i class="icon-leaf"></i>
                <h4><a href="<?= e(url('registro/vivero')) ?>">Tengo un vivero</a></h4>
                <p>Registrar mi vivero, inventario y productos para vender.</p>
                <p><a href="<?= e(url('registro/vivero')) ?>" class="btn-u">REGISTRAR MI VIVERO</a></p>
            </div>
        </div>
    </div><!--/row-->

    <p class="text-center margin-bottom-40">
        <a class="color-green" href="<?= e(url('cuenta')) ?>">¿Ya tienes cuenta? Entrar</a>
    </p>
</div><!--/container-->
<!--=== End Content Part ===-->
