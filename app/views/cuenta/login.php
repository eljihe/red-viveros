<?php
/** @var string|null $mensaje */
/** @var string|null $tipo */
?>
<!--=== Breadcrumbs ===-->
<div class="breadcrumbs margin-bottom-40">
    <div class="container">
        <h1 class="pull-left">Entrar</h1>
        <ul class="pull-right breadcrumb">
            <li><a href="<?= e(url('')) ?>">Inicio</a></li>
            <li><a href="<?= e(url('cuenta')) ?>">Mi cuenta</a></li>
            <li class="active">Entrar</li>
        </ul>
    </div><!--/container-->
</div><!--/breadcrumbs-->
<!--=== End Breadcrumbs ===-->

<!--=== Content Part ===-->
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
            <form class="reg-page" method="POST" action="<?= e(url('cuenta/entrar')) ?>">
                <div class="reg-header">
                    <h2>Entrar a mi cuenta</h2>
                </div>
                <?php if ($mensaje): ?>
                    <div class="alert <?= $tipo === 'ok' ? 'alert-success' : 'alert-danger' ?>"><?= e((string) $mensaje) ?></div>
                <?php endif; ?>
                <div class="input-group margin-bottom-20">
                    <span class="input-group-addon"><i class="icon-phone"></i></span>
                    <input type="text" name="telefono" inputmode="numeric" placeholder="Teléfono (10 dígitos)" maxlength="10" class="form-control" required>
                </div>
                <div class="input-group margin-bottom-20">
                    <span class="input-group-addon"><i class="icon-lock"></i></span>
                    <input type="password" name="pin" inputmode="numeric" placeholder="PIN (6 dígitos)" maxlength="6" class="form-control" required>
                </div>
                <?= csrfField() ?>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn-u pull-right" type="submit">ENTRAR</button>
                    </div>
                </div>
                <hr>
                <h4>¿Olvidaste tu PIN?</h4>
                <p>No te preocupes, <a class="color-green" href="<?= e(url('cuenta/olvide-pin')) ?>">haz clic aquí</a> para restablecerlo.</p>
                <h4>¿No tienes cuenta?</h4>
                <p><a class="color-green" href="<?= e(url('registro')) ?>">Regístrate</a>, es gratis.</p>
            </form>
        </div>
    </div><!--/row-->
</div><!--/container-->
<!--=== End Content Part ===-->
