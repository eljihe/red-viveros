<?php
/** @var array<string, mixed> $usuario */
/** @var array<string, mixed>|null $vivero */
/** @var array<int, array<string, mixed>> $productos */
/** @var string|null $mensaje */
/** @var string|null $tipo */
?>
<!--=== Breadcrumbs ===-->
<div class="breadcrumbs margin-bottom-40">
    <div class="container">
        <h1 class="pull-left">Mi cuenta</h1>
        <ul class="pull-right breadcrumb">
            <li><a href="<?= e(url('')) ?>">Inicio</a></li>
            <li class="active">Mi cuenta</li>
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
                <li class="list-group-item active"><a href="<?= e(url('cuenta')) ?>">Resumen</a></li>
                <?php if ($vivero !== null): ?>
                    <li class="list-group-item"><a href="<?= e(url('panel/vivero')) ?>">Editar mi vivero</a></li>
                    <li class="list-group-item"><a href="<?= e(url('panel/productos/nuevo')) ?>">+ Nuevo producto</a></li>
                <?php endif; ?>
                <li class="list-group-item"><a href="<?= e(url('cuenta/cambiar-pin')) ?>">Cambiar PIN</a></li>
                <li class="list-group-item">
                    <form method="POST" action="<?= e(url('cuenta/salir')) ?>" class="form-menu">
                        <?= csrfField() ?>
                        <button type="submit">Cerrar sesión</button>
                    </form>
                </li>
            </ul>
        </div>
        <!--End Sidebar Menu-->

        <!--Begin Content-->
        <div class="col-md-9">
            <?php if ($mensaje): ?>
                <div class="alert <?= $tipo === 'ok' ? 'alert-success' : 'alert-danger' ?>"><?= e((string) $mensaje) ?></div>
            <?php endif; ?>

            <!--Resumen-->
            <div class="panel panel-green margin-bottom-40">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="icon-user"></i> Hola, <?= e($usuario['nombre']) ?></h3>
                </div>
                <div class="panel-body">
                    <ul class="list-unstyled">
                        <li><i class="icon-tag color-green"></i> <strong>Rol:</strong> <?= e($usuario['rol_nombre']) ?></li>
                        <?php if ($vivero !== null): ?>
                            <li><i class="icon-leaf color-green"></i> <strong>Vivero:</strong> <?= e($vivero['nombre']) ?></li>
                            <li>
                                <i class="icon-flag color-green"></i> <strong>Estado:</strong>
                                <?php if ($vivero['estado'] === 'pendiente'): ?>
                                    <span class="label label-warning">Pendiente de aprobación</span>
                                <?php elseif ($vivero['estado'] === 'aprobado'): ?>
                                    <span class="label label-success">Aprobado</span>
                                <?php elseif ($vivero['estado'] === 'rechazado'): ?>
                                    <span class="label label-danger">Rechazado</span>
                                    <?php if ($vivero['motivo_estado']): ?> — <?= e($vivero['motivo_estado']) ?><?php endif; ?>
                                <?php elseif ($vivero['estado'] === 'suspendido'): ?>
                                    <span class="label label-danger">Suspendido</span>
                                    <?php if ($vivero['motivo_estado']): ?> — <?= e($vivero['motivo_estado']) ?><?php endif; ?>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <?php if ($vivero !== null): ?>
                        <p>
                            <a href="<?= e(url('panel/vivero')) ?>" class="btn-u btn-u-default">EDITAR MI VIVERO</a>
                            <a href="<?= e(url('panel/productos/nuevo')) ?>" class="btn-u">+ NUEVO PRODUCTO</a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <!--End Resumen-->

            <!--Mis productos-->
            <?php if ($vivero !== null): ?>
                <div class="panel panel-green margin-bottom-40">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="icon-shopping-cart"></i> Mis productos</h3>
                    </div>
                    <div class="panel-body">
                        <?php if (!empty($productos)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productos as $p): ?>
                                            <tr>
                                                <td class="col-foto">
                                                    <?php if ($p['foto_portada']): ?>
                                                        <img src="<?= e(url('uploads/productos/' . basename($p['foto_portada']))) ?>" alt="">
                                                    <?php else: ?>
                                                        <span class="sin-foto-mini"></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?= e($p['titulo']) ?></strong>
                                                    <?php if ((int) $p['activo'] === 0): ?> <span class="label label-default">Pausado</span><?php endif; ?>
                                                </td>
                                                <td><?= $p['precio'] !== null ? '$' . e(number_format((float) $p['precio'], 2)) . ' / ' . e($p['unidad']) : 'Precio a tratar' ?></td>
                                                <td>
                                                    <a href="<?= e(url('panel/productos/editar?id=' . (int) $p['id'])) ?>" class="btn-u btn-u-default btn-u-small">Editar</a>
                                                    <form method="POST" action="<?= e(url('panel/productos/eliminar')) ?>" class="form-inline-tabla" onsubmit="return confirm('¿Eliminar este producto?');">
                                                        <input type="hidden" name="id" value="<?= e((string) $p['id']) ?>">
                                                        <?= csrfField() ?>
                                                        <button type="submit" class="btn-u btn-u-red btn-u-small">Eliminar</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center">Aún no publicas productos. Usa "+ NUEVO PRODUCTO" para empezar a vender.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <!--End Mis productos-->
        </div>
        <!--End Content-->
    </div><!--/row-->
</div><!--/container-->
<!--=== End Content Part ===-->
