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
<!--=== Breadcrumbs ===-->
<div class="breadcrumbs margin-bottom-40">
    <div class="container">
        <h1 class="pull-left"><?= $esEdicion ? 'Editar producto' : 'Nuevo producto' ?></h1>
        <ul class="pull-right breadcrumb">
            <li><a href="<?= e(url('')) ?>">Inicio</a></li>
            <li><a href="<?= e(url('cuenta')) ?>">Mi cuenta</a></li>
            <li class="active"><?= $esEdicion ? 'Editar producto' : 'Nuevo producto' ?></li>
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

            <!--Datos del producto-->
            <div class="panel panel-green margin-bottom-40">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="icon-shopping-cart"></i> <?= $esEdicion ? 'Editar producto' : 'Nuevo producto' ?></h3>
                </div>
                <div class="panel-body">
                    <form method="POST" action="<?= e(url('panel/productos/guardar')) ?>" role="form">
                        <?php if ($esEdicion): ?>
                            <input type="hidden" name="id" value="<?= e((string) $producto['id']) ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="titulo">Título</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" maxlength="80" value="<?= e((string) ($producto['titulo'] ?? '')) ?>" placeholder="Ej. Plantas de cacao injertado" required>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="categoria_id">Categoría</label>
                                <select class="form-control" id="categoria_id" name="categoria_id" required>
                                    <option value="">Selecciona una categoría</option>
                                    <?php foreach ($categorias as $c): ?>
                                        <option value="<?= e((string) $c['id']) ?>" <?= (int) ($producto['categoria_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="unidad_id">Unidad</label>
                                <select class="form-control" id="unidad_id" name="unidad_id" required>
                                    <option value="">Selecciona una unidad</option>
                                    <?php foreach ($unidades as $u): ?>
                                        <option value="<?= e((string) $u['id']) ?>" <?= (int) ($producto['unidad_id'] ?? 0) === (int) $u['id'] ? 'selected' : '' ?>><?= e($u['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="precio">Precio en pesos (opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" id="precio" name="precio" min="0" step="0.01" value="<?= e((string) ($producto['precio'] ?? '')) ?>" placeholder="Vacío = Precio a tratar">
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="disponible">Cantidad disponible (opcional)</label>
                                <input type="number" class="form-control" id="disponible" name="disponible" min="0" step="1" value="<?= e((string) ($producto['disponible'] ?? '')) ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción (opcional)</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" maxlength="1000" rows="3" placeholder="Cuéntales a tus clientes sobre este producto..."><?= e((string) ($producto['descripcion'] ?? '')) ?></textarea>
                        </div>

                        <div class="checkbox">
                            <label for="hace_entrega">
                                <input type="checkbox" name="hace_entrega" id="hace_entrega" value="1"
                                       onchange="document.getElementById('campos-entrega').style.display = this.checked ? 'block' : 'none';"
                                       <?= !empty($producto['hace_entrega']) ? 'checked' : '' ?>> Hago entregas a domicilio
                            </label>
                        </div>

                        <div id="campos-entrega" style="display: <?= !empty($producto['hace_entrega']) ? 'block' : 'none' ?>;">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="entrega_km">Hasta cuántos km</label>
                                    <select class="form-control" id="entrega_km" name="entrega_km">
                                        <?php foreach ($kmEntregaValidos as $km): ?>
                                            <option value="<?= e((string) $km) ?>" <?= (int) ($producto['entrega_km'] ?? 0) === $km ? 'selected' : '' ?>><?= e((string) $km) ?> km</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="entrega_costo">Costo de entrega (opcional)</label>
                                    <input type="text" class="form-control" id="entrega_costo" name="entrega_costo" maxlength="60" value="<?= e((string) ($producto['entrega_costo'] ?? '')) ?>" placeholder="Gratis, $50 por viaje...">
                                </div>
                            </div>
                        </div>

                        <?= csrfField() ?>
                        <button type="submit" class="btn-u"><?= $esEdicion ? 'GUARDAR CAMBIOS' : 'PUBLICAR PRODUCTO' ?></button>
                    </form>
                </div>
            </div>
            <!--End Datos del producto-->

            <!--Fotos-->
            <?php if ($esEdicion): ?>
                <div class="panel panel-green margin-bottom-40">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="icon-camera"></i> Fotos del producto</h3>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="<?= e(url('panel/productos/fotos/subir')) ?>" enctype="multipart/form-data" role="form" class="margin-bottom-20">
                            <input type="hidden" name="producto_id" value="<?= e((string) $producto['id']) ?>">
                            <div class="form-group">
                                <label for="foto">Subir foto</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" required>
                                <small class="help-block">Máx 5 MB, formatos: JPEG, PNG, WebP</small>
                            </div>
                            <?= csrfField() ?>
                            <button type="submit" class="btn-u">SUBIR FOTO</button>
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
                            <p class="text-center">Aún no tienes fotos. ¡Sube la primera!</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="tag-box tag-box-v2">
                    <p>Primero guarda el producto con el botón de arriba; en cuanto se publique, aquí mismo podrás subirle fotos.</p>
                </div>
            <?php endif; ?>
            <!--End Fotos-->

            <p class="text-center margin-bottom-40">
                <a class="color-green" href="<?= e(url('cuenta')) ?>">← Volver a mi cuenta</a>
            </p>
        </div>
    </div><!--/row-->
</div><!--/container-->
<!--=== End Content Part ===-->
