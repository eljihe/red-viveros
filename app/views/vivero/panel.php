<?php
/** @var array<string, mixed> $vivero */
/** @var array<int, array<string, mixed>> $especies */
/** @var array<int, array<string, mixed>> $inventario */
/** @var array<int, array<string, mixed>> $fotos */
/** @var array<int, array<string, mixed>> $servicios */
/** @var string|null $mensaje */
/** @var string|null $tipo */
?>
<!--=== Breadcrumbs ===-->
<div class="breadcrumbs margin-bottom-40">
    <div class="container">
        <h1 class="pull-left">Mi vivero</h1>
        <ul class="pull-right breadcrumb">
            <li><a href="<?= e(url('')) ?>">Inicio</a></li>
            <li><a href="<?= e(url('cuenta')) ?>">Mi cuenta</a></li>
            <li class="active">Mi vivero</li>
        </ul>
    </div><!--/container-->
</div><!--/breadcrumbs-->
<!--=== End Breadcrumbs ===-->

<!--=== Content Part ===-->
<div class="container">
    <div class="row">
        <!--Begin Sidebar Menu-->
        <div class="col-md-3 md-margin-bottom-40">
            <ul class="list-group sidebar-nav-v1">
                <li class="list-group-item"><a href="#datos">Datos del vivero</a></li>
                <li class="list-group-item"><a href="#inventario">Inventario de plantas</a></li>
                <li class="list-group-item"><a href="#servicios">Talleres y servicios</a></li>
                <li class="list-group-item"><a href="#fotos">Fotos del vivero</a></li>
                <li class="list-group-item"><a href="<?= e(url('panel/productos/nuevo')) ?>">+ Vender un producto</a></li>
                <li class="list-group-item"><a href="<?= e(url('cuenta')) ?>">← Volver a mi cuenta</a></li>
            </ul>
        </div>
        <!--End Sidebar Menu-->

        <!--Begin Content-->
        <div class="col-md-9">
            <?php if ($mensaje): ?>
                <div class="alert <?= $tipo === 'ok' ? 'alert-success' : 'alert-danger' ?>"><?= e((string) $mensaje) ?></div>
            <?php endif; ?>

            <!--Datos del vivero-->
            <div class="panel panel-green margin-bottom-40" id="datos">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="icon-leaf"></i> Datos del vivero</h3>
                </div>
                <div class="panel-body">
                    <form method="POST" action="<?= e(url('panel/vivero/datos')) ?>" role="form">
                        <div class="form-group">
                            <label for="nombre">Nombre del vivero</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="80" value="<?= e($vivero['nombre']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="historia">Conoce al vivero (historia, opcional)</label>
                            <textarea class="form-control" id="historia" name="historia" maxlength="1000" rows="3" placeholder="Cuéntanos sobre tu vivero..."><?= e($vivero['historia'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="integrantes">Cantidad de integrantes (opcional)</label>
                            <input type="number" class="form-control" id="integrantes" name="integrantes" min="1" step="1" value="<?= e($vivero['integrantes'] ?? '') ?>">
                        </div>

                        <div class="form-group" id="mapa-container">
                            <label>Ubicación del vivero</label>
                            <div id="mapa-edicion" style="height: 280px; border: 1px solid #ddd;"></div>
                        </div>

                        <div class="form-group">
                            <label for="coordenadas-input">Coordenadas (lat, lng)</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="coordenadas" id="coordenadas-input" placeholder="18.2645, -93.2213" value="<?= e($vivero['lat'] . ', ' . $vivero['lng']) ?>" required>
                                <span class="input-group-btn">
                                    <button type="button" class="btn-u btn-u-default" onclick="usarMiUbicacion(this)">Usar mi ubicación</button>
                                </span>
                            </div>
                            <small class="help-block">Formato: latitud, longitud (ej. 18.2645, -93.2213) o grados/minutos/segundos (ej. 18°12'04.3"N 93°13'28.2"W)</small>
                        </div>

                        <?= csrfField() ?>
                        <button type="submit" class="btn-u">GUARDAR DATOS</button>
                    </form>
                </div>
            </div>
            <!--End Datos del vivero-->

            <!--Inventario-->
            <div class="panel panel-green margin-bottom-40" id="inventario">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="icon-tasks"></i> Inventario de plantas</h3>
                </div>
                <div class="panel-body">
                    <form method="POST" action="<?= e(url('panel/vivero/inventario/guardar')) ?>" role="form" class="margin-bottom-20">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="especie_id">Especie</label>
                                <select class="form-control" id="especie_id" name="especie_id" required>
                                    <option value="">Selecciona una especie</option>
                                    <?php foreach ($especies as $esp): ?>
                                        <option value="<?= e((string) $esp['id']) ?>"><?= e($esp['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="cantidad">Cantidad</label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" min="0" step="1" value="0" required>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="edad_meses">Edad (meses)</label>
                                <input type="number" class="form-control" id="edad_meses" name="edad_meses" min="0" max="255" step="1" placeholder="opcional">
                            </div>
                        </div>
                        <?= csrfField() ?>
                        <button type="submit" class="btn-u">AGREGAR ESPECIE</button>
                    </form>

                    <?php if (!empty($inventario)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
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
                                                <form method="POST" action="<?= e(url('panel/vivero/inventario/eliminar')) ?>" class="form-inline-tabla">
                                                    <input type="hidden" name="especie_id" value="<?= e((string) $item['especie_id']) ?>">
                                                    <?= csrfField() ?>
                                                    <button type="submit" class="btn-u btn-u-red btn-u-small">Quitar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">Aún no has registrado especies en tu inventario.</p>
                    <?php endif; ?>
                </div>
            </div>
            <!--End Inventario-->

            <!--Servicios-->
            <div class="panel panel-green margin-bottom-40" id="servicios">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="icon-wrench"></i> Talleres y servicios</h3>
                </div>
                <div class="panel-body">
                    <p>Ej. Taller de injerto de cacao, taller de composta. Se muestran en la ficha de tu vivero en el mapa, sin necesidad de registro.</p>
                    <form method="POST" action="<?= e(url('panel/vivero/servicios/guardar')) ?>" role="form" class="margin-bottom-20">
                        <div class="form-group">
                            <label for="titulo">Nombre del servicio</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" maxlength="80" placeholder="Ej. Taller de injerto de cacao" required>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="horario">Horario (opcional)</label>
                                <input type="text" class="form-control" id="horario" name="horario" maxlength="120" placeholder="Ej. Sábados 9am, Bajo cita previa...">
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="costo">Costo (opcional)</label>
                                <input type="text" class="form-control" id="costo" name="costo" maxlength="60" placeholder="Ej. Gratis, $150 por persona...">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción (opcional)</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" maxlength="500" rows="2" placeholder="Cuéntales a los interesados qué incluye..."></textarea>
                        </div>
                        <?= csrfField() ?>
                        <button type="submit" class="btn-u">AGREGAR SERVICIO</button>
                    </form>

                    <?php if (!empty($servicios)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Servicio</th>
                                        <th>Horario</th>
                                        <th>Costo</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($servicios as $s): ?>
                                        <tr>
                                            <td><?= e($s['titulo']) ?></td>
                                            <td><?= e($s['horario'] ?? '—') ?></td>
                                            <td><?= e($s['costo'] ?? '—') ?></td>
                                            <td>
                                                <form method="POST" action="<?= e(url('panel/vivero/servicios/eliminar')) ?>" class="form-inline-tabla">
                                                    <input type="hidden" name="id" value="<?= e((string) $s['id']) ?>">
                                                    <?= csrfField() ?>
                                                    <button type="submit" class="btn-u btn-u-red btn-u-small">Quitar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">Aún no has agregado servicios.</p>
                    <?php endif; ?>
                </div>
            </div>
            <!--End Servicios-->

            <!--Fotos-->
            <div class="panel panel-green margin-bottom-40" id="fotos">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="icon-camera"></i> Fotos del vivero</h3>
                </div>
                <div class="panel-body">
                    <form method="POST" action="<?= e(url('panel/vivero/fotos/subir')) ?>" enctype="multipart/form-data" role="form" class="margin-bottom-20">
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
                        <p class="text-center">Aún no tienes fotos. ¡Sube tu primera foto!</p>
                    <?php endif; ?>
                </div>
            </div>
            <!--End Fotos-->
        </div>
        <!--End Content-->
    </div><!--/row-->
</div><!--/container-->
<!--=== End Content Part ===-->
