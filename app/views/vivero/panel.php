<?php
/** @var array<string, mixed> $vivero */
/** @var array<int, array<string, mixed>> $especies */
/** @var array<int, array<string, mixed>> $inventario */
/** @var array<int, array<string, mixed>> $fotos */
/** @var string|null $mensaje */
/** @var string|null $tipo */
?>
<div class="panel">
    <h2>Mi vivero</h2>
    <?php if ($mensaje): ?>
        <div class="msg <?= e($tipo === 'ok' ? 'ok' : 'err') ?>">
            <?= e((string) $mensaje) ?>
        </div>
    <?php endif; ?>

    <!-- SECCIÓN: DATOS -->
    <fieldset style="border: none; padding: 0; margin-bottom: 30px;">
        <legend>Datos del vivero</legend>
        <form method="POST" action="<?= e(url('panel/vivero/datos')) ?>">
            <div class="field">
                <span>Nombre del vivero</span>
                <input type="text" name="nombre" maxlength="80" value="<?= e($vivero['nombre']) ?>" required>
            </div>
            <div class="field">
                <span>Conoce al vivero (historia, opcional)</span>
                <textarea name="historia" maxlength="1000" rows="3" placeholder="Cuéntanos sobre tu vivero..."><?= e($vivero['historia'] ?? '') ?></textarea>
            </div>
            <div class="field">
                <span>Cantidad de integrantes (opcional)</span>
                <input type="number" name="integrantes" min="1" step="1" value="<?= e($vivero['integrantes'] ?? '') ?>">
            </div>

            <div id="mapa-container" style="margin-bottom: 15px;">
                <span style="display: block; margin-bottom: 10px;">Ubicación del vivero</span>
                <div id="mapa-edicion" style="height: 280px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px;"></div>
            </div>

            <div class="field">
                <span>Coordenadas (lat, lng)</span>
                <input type="text" name="coordenadas" id="coordenadas-input" placeholder="18.2645, -93.2213" value="<?= e($vivero['lat'] . ', ' . $vivero['lng']) ?>" required>
                <button type="button" class="btn" onclick="usarMiUbicacion(this)">Usar mi ubicación</button>
                <small>Formato: latitud, longitud</small>
            </div>

            <?= csrfField() ?>
            <button type="submit" class="btn pri">Guardar datos</button>
        </form>
    </fieldset>

    <!-- SECCIÓN: INVENTARIO -->
    <fieldset style="border: none; padding: 0; margin-bottom: 30px;">
        <legend>Inventario de plantas</legend>

        <form method="POST" action="<?= e(url('panel/vivero/inventario/guardar')) ?>" style="margin-bottom: 20px;">
            <div class="row2">
                <div class="field">
                    <span>Especie</span>
                    <select name="especie_id" required>
                        <option value="">Selecciona una especie</option>
                        <?php foreach ($especies as $e): ?>
                            <option value="<?= e((string) $e['id']) ?>"><?= e($e['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <span>Cantidad</span>
                    <input type="number" name="cantidad" min="0" step="1" value="0" required>
                </div>
            </div>
            <div class="field">
                <span>Edad (meses, opcional)</span>
                <input type="number" name="edad_meses" min="0" max="255" step="1">
            </div>
            <?= csrfField() ?>
            <button type="submit" class="btn pri">Agregar especie</button>
        </form>

        <?php if (!empty($inventario)): ?>
            <table class="tabla-simple">
                <thead>
                    <tr>
                        <th>Especie</th>
                        <th>Cantidad</th>
                        <th>Edad (meses)</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventario as $item): ?>
                        <tr>
                            <td><?= e($item['especie_nombre']) ?></td>
                            <td><?= e((string) $item['cantidad']) ?></td>
                            <td><?= e($item['edad_meses'] !== null ? (string) $item['edad_meses'] : '—') ?></td>
                            <td>
                                <form method="POST" action="<?= e(url('panel/vivero/inventario/eliminar')) ?>" style="display: inline;">
                                    <input type="hidden" name="especie_id" value="<?= e((string) $item['especie_id']) ?>">
                                    <?= csrfField() ?>
                                    <button type="submit" class="btn" style="font-size: 0.85em;">Quitar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #666; text-align: center;">Aún no has registrado especies en tu inventario.</p>
        <?php endif; ?>
    </fieldset>

    <!-- SECCIÓN: FOTOS -->
    <fieldset style="border: none; padding: 0;">
        <legend>Fotos del vivero</legend>

        <form method="POST" action="<?= e(url('panel/vivero/fotos/subir')) ?>" enctype="multipart/form-data" style="margin-bottom: 20px;">
            <div class="field">
                <span>Subir foto</span>
                <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" required>
                <small>Máx 5 MB, formatos: JPEG, PNG, WebP</small>
            </div>
            <?= csrfField() ?>
            <button type="submit" class="btn pri">Subir foto</button>
        </form>

        <?php if (!empty($fotos)): ?>
            <div class="fotos-grid">
                <?php foreach ($fotos as $foto): ?>
                    <div class="foto-item">
                        <img src="<?= e(url('uploads/viveros/' . basename($foto['archivo']))) ?>" alt="Foto del vivero" loading="lazy">
                        <form method="POST" action="<?= e(url('panel/vivero/fotos/eliminar')) ?>" class="foto-overlay">
                            <input type="hidden" name="foto_id" value="<?= e((string) $foto['id']) ?>">
                            <?= csrfField() ?>
                            <button type="submit" class="btn-eliminar" title="Eliminar foto">✕</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: #666; text-align: center;">Aún no tienes fotos. ¡Sube tu primera foto!</p>
        <?php endif; ?>
    </fieldset>

    <p style="text-align: center; margin-top: 30px;">
        <a href="<?= e(url('cuenta')) ?>">← Volver a mi cuenta</a>
    </p>
</div>
