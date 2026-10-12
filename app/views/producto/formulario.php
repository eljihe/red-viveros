<?php
/** @var array<string, mixed>|null $producto */
/** @var array<int, array<string, mixed>> $fotos */
/** @var array<int, array<string, mixed>> $categorias */
/** @var array<int, array<string, mixed>> $unidades */
/** @var array<int, int> $kmEntregaValidos */
/** @var string|null $mensaje */
/** @var string|null $tipo */

$esEdicion = $producto !== null;
?>
<div class="panel">
    <h2><?= $esEdicion ? 'Editar producto' : 'Nuevo producto' ?></h2>
    <?php if ($mensaje): ?>
        <div class="msg <?= e($tipo === 'ok' ? 'ok' : 'err') ?>">
            <?= e((string) $mensaje) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= e(url('panel/productos/guardar')) ?>">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id" value="<?= e((string) $producto['id']) ?>">
        <?php endif; ?>

        <div class="field">
            <span>Título</span>
            <input type="text" name="titulo" maxlength="80" value="<?= e((string) ($producto['titulo'] ?? '')) ?>" placeholder="Ej. Plantas de cacao injertado" required>
        </div>

        <div class="row2">
            <div class="field">
                <span>Categoría</span>
                <select name="categoria_id" required>
                    <option value="">Selecciona una categoría</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= e((string) $c['id']) ?>" <?= (int) ($producto['categoria_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <span>Unidad</span>
                <select name="unidad_id" required>
                    <option value="">Selecciona una unidad</option>
                    <?php foreach ($unidades as $u): ?>
                        <option value="<?= e((string) $u['id']) ?>" <?= (int) ($producto['unidad_id'] ?? 0) === (int) $u['id'] ? 'selected' : '' ?>><?= e($u['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row2">
            <div class="field">
                <span>Precio en pesos (opcional)</span>
                <input type="number" name="precio" min="0" step="0.01" value="<?= e((string) ($producto['precio'] ?? '')) ?>" placeholder="Vacío = Precio a tratar">
            </div>
            <div class="field">
                <span>Cantidad disponible (opcional)</span>
                <input type="number" name="disponible" min="0" step="1" value="<?= e((string) ($producto['disponible'] ?? '')) ?>">
            </div>
        </div>

        <div class="field">
            <span>Descripción (opcional)</span>
            <textarea name="descripcion" maxlength="1000" rows="3" placeholder="Cuéntales a tus clientes sobre este producto..."><?= e((string) ($producto['descripcion'] ?? '')) ?></textarea>
        </div>

        <div class="check">
            <input type="checkbox" name="hace_entrega" id="hace_entrega" value="1"
                   onchange="document.getElementById('campos-entrega').style.display = this.checked ? 'block' : 'none';"
                   <?= !empty($producto['hace_entrega']) ? 'checked' : '' ?>>
            <label for="hace_entrega">Hago entregas a domicilio</label>
        </div>

        <div id="campos-entrega" style="display: <?= !empty($producto['hace_entrega']) ? 'block' : 'none' ?>; margin-bottom: 15px;">
            <div class="row2">
                <div class="field">
                    <span>Hasta cuántos km</span>
                    <select name="entrega_km">
                        <?php foreach ($kmEntregaValidos as $km): ?>
                            <option value="<?= e((string) $km) ?>" <?= (int) ($producto['entrega_km'] ?? 0) === $km ? 'selected' : '' ?>><?= e((string) $km) ?> km</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <span>Costo de entrega (opcional)</span>
                    <input type="text" name="entrega_costo" maxlength="60" value="<?= e((string) ($producto['entrega_costo'] ?? '')) ?>" placeholder="Gratis, $50 por viaje...">
                </div>
            </div>
        </div>

        <?= csrfField() ?>
        <button type="submit" class="btn pri"><?= $esEdicion ? 'Guardar cambios' : 'Publicar producto' ?></button>
    </form>

    <?php if ($esEdicion): ?>
        <fieldset style="border: none; padding: 0; margin-top: 30px;">
            <legend>Fotos del producto</legend>

            <form method="POST" action="<?= e(url('panel/productos/fotos/subir')) ?>" enctype="multipart/form-data" style="margin-bottom: 20px;">
                <input type="hidden" name="producto_id" value="<?= e((string) $producto['id']) ?>">
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
                            <img src="<?= e(url('uploads/productos/' . basename($foto['archivo']))) ?>" alt="Foto del producto" loading="lazy">
                            <form method="POST" action="<?= e(url('panel/productos/fotos/eliminar')) ?>" class="foto-overlay">
                                <input type="hidden" name="foto_id" value="<?= e((string) $foto['id']) ?>">
                                <input type="hidden" name="producto_id" value="<?= e((string) $producto['id']) ?>">
                                <?= csrfField() ?>
                                <button type="submit" class="btn-eliminar" title="Eliminar foto">✕</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: var(--muted); text-align: center;">Aún no tienes fotos. ¡Sube la primera!</p>
            <?php endif; ?>
        </fieldset>
    <?php else: ?>
        <p class="hint" style="margin-top: 20px;">Guarda el producto primero para poder agregarle fotos.</p>
    <?php endif; ?>

    <p style="text-align: center; margin-top: 30px;">
        <a href="<?= e(url('cuenta')) ?>">← Volver a mi cuenta</a>
    </p>
</div>
