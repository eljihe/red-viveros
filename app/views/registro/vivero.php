<?php
/** @var array<int, array<string, mixed>> $municipios */
/** @var array<int, array<string, mixed>> $comunidades */
/** @var string|null $mensaje */
/** @var string|null $tipo */
/** @var array<string, mixed> $formData */
?>
<!--=== Breadcrumbs ===-->
<div class="breadcrumbs margin-bottom-40">
    <div class="container">
        <h1 class="pull-left">Registro de vivero</h1>
        <ul class="pull-right breadcrumb">
            <li><a href="<?= e(url('')) ?>">Inicio</a></li>
            <li><a href="<?= e(url('registro')) ?>">Registrarme</a></li>
            <li class="active">Vivero</li>
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

            <form method="POST" action="<?= e(url('registro/vivero')) ?>" role="form">
                <!--Tu información-->
                <div class="panel panel-green margin-bottom-40">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="icon-user"></i> Tu información</h3>
                    </div>
                    <div class="panel-body">
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
                    </div>
                </div>
                <!--End Tu información-->

                <!--Información del vivero-->
                <div class="panel panel-green margin-bottom-40">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="icon-leaf"></i> Información del vivero</h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="nombre_vivero">Nombre del vivero</label>
                            <input type="text" class="form-control" id="nombre_vivero" name="nombre_vivero" maxlength="80" value="<?= e($formData['nombre_vivero'] ?? '') ?>" required>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="municipio_select">Municipio</label>
                                <select class="form-control" name="municipio_id" id="municipio_select" required>
                                    <option value="">Selecciona un municipio</option>
                                    <?php foreach ($municipios as $m): ?>
                                        <option value="<?= e((string) $m['id']) ?>" <?= (($formData['municipio_id'] ?? 0) == $m['id']) ? 'selected' : '' ?>><?= e($m['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="comunidad_select">Comunidad</label>
                                <select class="form-control" name="comunidad_id" id="comunidad_select" required>
                                    <option value="">Selecciona municipio primero</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="historia">Conoce al vivero (historia, opcional)</label>
                            <textarea class="form-control" id="historia" name="historia" maxlength="1000" rows="4" placeholder="Cuéntanos sobre tu vivero..."><?= e($formData['historia'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="integrantes">Cantidad de integrantes (opcional)</label>
                            <input type="number" class="form-control" id="integrantes" name="integrantes" min="1" step="1" value="<?= e($formData['integrantes'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="coordenadas">Ubicación</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="coordenadas" name="coordenadas" placeholder="18.2645, -93.2213" data-gps-target value="<?= e($formData['coordenadas'] ?? '') ?>" required>
                                <span class="input-group-btn">
                                    <button type="button" class="btn-u btn-u-default" onclick="usarMiUbicacion(this)">Usar mi ubicación</button>
                                </span>
                            </div>
                            <small class="help-block">Formato: latitud, longitud (ej. 18.2645, -93.2213) o grados/minutos/segundos (ej. 18°12'04.3"N 93°13'28.2"W)</small>
                        </div>
                    </div>
                </div>
                <!--End Información del vivero-->

                <div class="checkbox">
                    <label for="acepto">
                        <input type="checkbox" name="acepto_terminos" id="acepto" required> Acepto los términos de uso y el aviso de privacidad
                    </label>
                </div>
                <?= csrfField() ?>
                <button type="submit" class="btn-u">CREAR VIVERO</button>
            </form>
            <hr>
            <p class="text-center margin-bottom-40">
                <a class="color-green" href="<?= e(url('registro')) ?>">Atrás</a> ·
                <a class="color-green" href="<?= e(url('cuenta')) ?>">¿Ya tienes cuenta?</a>
            </p>
        </div>
    </div><!--/row-->
</div><!--/container-->
<!--=== End Content Part ===-->

<script>
window.comunidadesPorMunicipio = <?= json_encode(
    array_reduce($comunidades, function ($carry, $c) {
        $mid = (int) $c['municipio_id'];
        if (!isset($carry[$mid])) {
            $carry[$mid] = [];
        }
        $carry[$mid][] = $c;
        return $carry;
    }, [])
) ?>;
window.comunidadIdGuardada = <?= (int) ($formData['comunidad_id'] ?? 0) ?>;

document.addEventListener('DOMContentLoaded', function() {
    const comunidadSelect = document.getElementById('comunidad_select');
    if (comunidadSelect && window.comunidadIdGuardada > 0) {
        comunidadSelect.dataset.savedValue = window.comunidadIdGuardada;
    }
});
</script>
