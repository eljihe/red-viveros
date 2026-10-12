<?php
/** @var array<int, array<string, mixed>> $municipios */
/** @var string|null $mensaje */
/** @var string|null $tipo */
/** @var array<string, mixed> $formData */
?>
<div class="panel">
    <h2>Registrarse como comprador</h2>
    <?php if ($mensaje): ?>
        <div class="msg <?= e($tipo === 'ok' ? 'ok' : 'err') ?>">
            <?= e((string) $mensaje) ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="<?= e(url('registro/comprador')) ?>">
        <div class="row2">
            <div class="field">
                <span>Nombre</span>
                <input type="text" name="nombre" maxlength="60" value="<?= e($formData['nombre'] ?? '') ?>" required>
            </div>
            <div class="field">
                <span>Apellidos</span>
                <input type="text" name="apellidos" maxlength="80" value="<?= e($formData['apellidos'] ?? '') ?>" required>
            </div>
        </div>
        <div class="row2">
            <div class="field">
                <span>Teléfono</span>
                <input type="text" name="telefono" inputmode="numeric" placeholder="1234567890" maxlength="10" value="<?= e($formData['telefono'] ?? '') ?>" required>
            </div>
            <div class="field">
                <span>Email (opcional)</span>
                <input type="email" name="email" maxlength="120" value="<?= e($formData['email'] ?? '') ?>">
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
        <div class="field">
            <span>Municipio</span>
            <select name="municipio_id" required>
                <option value="">Selecciona un municipio</option>
                <?php foreach ($municipios as $m): ?>
                    <option value="<?= e((string) $m['id']) ?>" <?= ($formData['municipio_id'] == $m['id']) ? 'selected' : '' ?>><?= e($m['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <span>Comunidad o colonia</span>
            <input type="text" name="comunidad_colonia" maxlength="80" value="<?= e($formData['comunidad_colonia'] ?? '') ?>" required>
        </div>
        <div class="field">
            <span>¿Qué tipo de comprador eres?</span>
            <select name="tipo_comprador">
                <option value="particular" <?= ($formData['tipo_comprador'] ?? '') === 'particular' ? 'selected' : '' ?>>Particular</option>
                <option value="tienda_negocio" <?= ($formData['tipo_comprador'] ?? '') === 'tienda_negocio' ? 'selected' : '' ?>>Tienda o negocio</option>
                <option value="acopiador" <?= ($formData['tipo_comprador'] ?? '') === 'acopiador' ? 'selected' : '' ?>>Acopiador</option>
            </select>
        </div>
        <div class="field">
            <span>RFC (opcional)</span>
            <input type="text" name="rfc" maxlength="13" placeholder="ABC123456XYZ" value="<?= e($formData['rfc'] ?? '') ?>">
        </div>
        <div class="field">
            <span>Ubicación (opcional)</span>
            <input type="text" name="coordenadas" placeholder="18.2645, -93.2213" data-gps-target value="<?= e($formData['coordenadas'] ?? '') ?>">
            <button type="button" class="btn" onclick="usarMiUbicacion(this)">Usar mi ubicación</button>
            <small>Formato: latitud, longitud (ej. 18.2645, -93.2213)</small>
        </div>
        <div class="check">
            <input type="checkbox" name="acepto_terminos" id="acepto" required>
            <label for="acepto">Acepto los términos de uso y el aviso de privacidad</label>
        </div>
        <?= csrfField() ?>
        <button type="submit" class="btn pri">Crear cuenta</button>
    </form>
    <p style="text-align: center; margin-top: 20px;">
        <a href="<?= e(url('registro')) ?>">Atrás</a> ·
        <a href="<?= e(url('cuenta')) ?>">¿Ya tienes cuenta?</a>
    </p>
</div>
<script src="<?= e(url('js/formularios.js')) ?>"></script>
