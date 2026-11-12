<?php
/** @var array<int, array<string, mixed>> $municipios */
/** @var string|null $mensaje */
/** @var string|null $tipo */
/** @var array<string, mixed> $formData */
?>
<!--=== Breadcrumbs ===-->
<div class="breadcrumbs margin-bottom-40">
    <div class="container">
        <h1 class="pull-left">Registro de comprador</h1>
        <ul class="pull-right breadcrumb">
            <li><a href="<?= e(url('')) ?>">Inicio</a></li>
            <li><a href="<?= e(url('registro')) ?>">Registrarme</a></li>
            <li class="active">Comprador</li>
        </ul>
    </div><!--/container-->
</div><!--/breadcrumbs-->
<!--=== End Breadcrumbs ===-->

<!--=== Content Part ===-->
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <?php if ($mensaje): ?>
                <div class="alert <?= $tipo === 'ok' ? 'alert-success' : 'alert-danger' ?>"><?= e((string) $mensaje) ?></div>
            <?php endif; ?>

            <div class="panel panel-green margin-bottom-40">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="icon-user"></i> Registrarse como comprador</h3>
                </div>
                <div class="panel-body">
                    <form method="POST" action="<?= e(url('registro/comprador')) ?>" role="form">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" maxlength="60" value="<?= e($formData['nombre'] ?? '') ?>" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="apellidos">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" maxlength="80" value="<?= e($formData['apellidos'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="telefono">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" inputmode="numeric" placeholder="1234567890" maxlength="10" value="<?= e($formData['telefono'] ?? '') ?>" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="email">Email (opcional)</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="120" value="<?= e($formData['email'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="pin">PIN (6 dígitos)</label>
                                <input type="password" class="form-control" id="pin" name="pin" inputmode="numeric" placeholder="000000" maxlength="6" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="pin_confirmacion">Confirmar PIN</label>
                                <input type="password" class="form-control" id="pin_confirmacion" name="pin_confirmacion" inputmode="numeric" placeholder="000000" maxlength="6" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="municipio_id">Municipio</label>
                                <select class="form-control" id="municipio_id" name="municipio_id" required>
                                    <option value="">Selecciona un municipio</option>
                                    <?php foreach ($municipios as $m): ?>
                                        <option value="<?= e((string) $m['id']) ?>" <?= ($formData['municipio_id'] ?? null) == $m['id'] ? 'selected' : '' ?>><?= e($m['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="comunidad_colonia">Comunidad o colonia</label>
                                <input type="text" class="form-control" id="comunidad_colonia" name="comunidad_colonia" maxlength="80" value="<?= e($formData['comunidad_colonia'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="tipo_comprador">¿Qué tipo de comprador eres?</label>
                                <select class="form-control" id="tipo_comprador" name="tipo_comprador">
                                    <option value="particular" <?= ($formData['tipo_comprador'] ?? '') === 'particular' ? 'selected' : '' ?>>Particular</option>
                                    <option value="tienda_negocio" <?= ($formData['tipo_comprador'] ?? '') === 'tienda_negocio' ? 'selected' : '' ?>>Tienda o negocio</option>
                                    <option value="acopiador" <?= ($formData['tipo_comprador'] ?? '') === 'acopiador' ? 'selected' : '' ?>>Acopiador</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="rfc">RFC (opcional)</label>
                                <input type="text" class="form-control" id="rfc" name="rfc" maxlength="13" placeholder="ABC123456XYZ" value="<?= e($formData['rfc'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="coordenadas">Ubicación (opcional)</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="coordenadas" name="coordenadas" placeholder="18.2645, -93.2213" data-gps-target value="<?= e($formData['coordenadas'] ?? '') ?>">
                                <span class="input-group-btn">
                                    <button type="button" class="btn-u btn-u-default" onclick="usarMiUbicacion(this)">Usar mi ubicación</button>
                                </span>
                            </div>
                            <small class="help-block">Formato: latitud, longitud (ej. 18.2645, -93.2213)</small>
                        </div>
                        <div class="checkbox">
                            <label for="acepto">
                                <input type="checkbox" name="acepto_terminos" id="acepto" required> Acepto los términos de uso y el aviso de privacidad
                            </label>
                        </div>
                        <?= csrfField() ?>
                        <button type="submit" class="btn-u">CREAR CUENTA</button>
                    </form>
                    <hr>
                    <p class="text-center">
                        <a class="color-green" href="<?= e(url('registro')) ?>">Atrás</a> ·
                        <a class="color-green" href="<?= e(url('cuenta')) ?>">¿Ya tienes cuenta?</a>
                    </p>
                </div>
            </div>
        </div>
    </div><!--/row-->
</div><!--/container-->
<!--=== End Content Part ===-->
