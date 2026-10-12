<?php
/** @var array<int, array<string, mixed>> $municipios */
/** @var array<int, array<string, mixed>> $comunidades */
?>
<div class="panel">
    <h2>Registrar mi vivero</h2>
    <form method="POST" action="<?= e(url('registro/vivero')) ?>">
        <fieldset style="border: none; padding: 0;">
            <legend>Tu información</legend>
            <div class="row2">
                <div class="field">
                    <span>Nombre</span>
                    <input type="text" name="nombre" maxlength="60" required>
                </div>
                <div class="field">
                    <span>Apellidos</span>
                    <input type="text" name="apellidos" maxlength="80" required>
                </div>
            </div>
            <div class="row2">
                <div class="field">
                    <span>Teléfono</span>
                    <input type="text" name="telefono" inputmode="numeric" placeholder="1234567890" maxlength="10" required>
                </div>
                <div class="field">
                    <span>Email (opcional)</span>
                    <input type="email" name="email" maxlength="120">
                </div>
            </div>
            <div class="row2">
                <div class="field">
                    <span>PIN (6 dígitos)</span>
                    <input type="text" name="pin" inputmode="numeric" placeholder="000000" maxlength="6" required>
                </div>
                <div class="field">
                    <span>Confirmar PIN</span>
                    <input type="text" name="pin_confirmacion" inputmode="numeric" placeholder="000000" maxlength="6" required>
                </div>
            </div>
        </fieldset>

        <fieldset style="border: none; padding: 0; margin-top: 20px;">
            <legend>Información del vivero</legend>
            <div class="field">
                <span>Nombre del vivero</span>
                <input type="text" name="nombre_vivero" maxlength="80" required>
            </div>
            <div class="row2">
                <div class="field">
                    <span>Municipio</span>
                    <select name="municipio_id" id="municipio_select" required>
                        <option value="">Selecciona un municipio</option>
                        <?php foreach ($municipios as $m): ?>
                            <option value="<?= e((string) $m['id']) ?>"><?= e($m['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <span>Comunidad</span>
                    <select name="comunidad_id" id="comunidad_select" required>
                        <option value="">Selecciona municipio primero</option>
                    </select>
                </div>
            </div>
            <div class="field">
                <span>Conoce al vivero (historia, opcional)</span>
                <textarea name="historia" maxlength="1000" rows="4" placeholder="Cuéntanos sobre tu vivero..."></textarea>
            </div>
            <div class="field">
                <span>Cantidad de integrantes (opcional)</span>
                <input type="number" name="integrantes" min="1" step="1">
            </div>
            <div class="field">
                <span>Ubicación</span>
                <input type="text" name="coordenadas" placeholder="18.2645, -93.2213" data-gps-target required>
                <button type="button" class="btn" onclick="usarMiUbicacion(this)">Usar mi ubicación</button>
                <small>Formato: latitud, longitud (ej. 18.2645, -93.2213)</small>
            </div>
        </fieldset>

        <div class="check" style="margin-top: 20px;">
            <input type="checkbox" name="acepto_terminos" id="acepto" required>
            <label for="acepto">Acepto los términos de uso y el aviso de privacidad</label>
        </div>
        <?= csrfField() ?>
        <button type="submit" class="btn pri">Crear vivero</button>
    </form>
    <p style="text-align: center; margin-top: 20px;">
        <a href="<?= e(url('registro')) ?>">Atrás</a> ·
        <a href="<?= e(url('cuenta')) ?>">¿Ya tienes cuenta?</a>
    </p>
</div>

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
</script>
<script src="<?= e(url('js/formularios.js')) ?>"></script>
