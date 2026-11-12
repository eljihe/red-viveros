<?php
/** @var string|null $enlaceWhatsApp */
?>
<!--=== Breadcrumbs ===-->
<div class="breadcrumbs margin-bottom-40">
    <div class="container">
        <h1 class="pull-left">Olvidé mi PIN</h1>
        <ul class="pull-right breadcrumb">
            <li><a href="<?= e(url('')) ?>">Inicio</a></li>
            <li><a href="<?= e(url('cuenta')) ?>">Mi cuenta</a></li>
            <li class="active">Olvidé mi PIN</li>
        </ul>
    </div><!--/container-->
</div><!--/breadcrumbs-->
<!--=== End Breadcrumbs ===-->

<!--=== Content Part ===-->
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
            <div class="reg-page">
                <div class="reg-header">
                    <h2>Olvidé mi PIN</h2>
                </div>
                <p>Para restablecer tu PIN, escribe al administrador por WhatsApp.</p>
                <?php if ($enlaceWhatsApp): ?>
                    <p><a href="<?= e($enlaceWhatsApp) ?>" class="btn-u btn-whatsapp" target="_blank" rel="noopener">CONTACTAR POR WHATSAPP</a></p>
                <?php else: ?>
                    <p>No hay un administrador disponible en este momento.</p>
                <?php endif; ?>
                <hr>
                <p><a class="color-green" href="<?= e(url('cuenta')) ?>">Volver a entrar</a></p>
            </div>
        </div>
    </div><!--/row-->
</div><!--/container-->
<!--=== End Content Part ===-->
