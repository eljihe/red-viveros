<?php
/** @var array<int, array<string, mixed>> $productos */
/** @var array<int, array<string, mixed>> $categorias */
/** @var array<int, array<string, mixed>> $municipios */
/** @var array<string, int> $conteoCategorias */
/** @var array{q: string, categoria_id: int, municipio_id: int, entrega: bool} $filtros */

$enlaceCategoria = static function (int $categoriaId) use ($filtros): string {
    $params = array_filter([
        'q' => $filtros['q'],
        'categoria' => $categoriaId > 0 ? $categoriaId : null,
        'municipio' => $filtros['municipio_id'] > 0 ? $filtros['municipio_id'] : null,
        'entrega' => $filtros['entrega'] ? 1 : null,
    ], static fn ($v): bool => $v !== null && $v !== '');

    return url('mercado') . ($params ? '?' . http_build_query($params) : '');
};
?>
<!--=== Breadcrumbs ===-->
<div class="breadcrumbs margin-bottom-40">
    <div class="container">
        <h1 class="pull-left">Mercado</h1>
        <ul class="pull-right breadcrumb">
            <li><a href="<?= e(url('')) ?>">Inicio</a></li>
            <li class="active">Mercado</li>
        </ul>
    </div><!--/container-->
</div><!--/breadcrumbs-->
<!--=== End Breadcrumbs ===-->

<!--=== Content Part ===-->
<div class="container mercado-ancho">
    <div class="headline"><h2>Productos de nuestros viveros</h2></div>
    <p class="margin-bottom-30">Plantas, frutas y cacao directo de los grupos de Sembrando Vida. Regístrate gratis para ver el teléfono y escribirles por WhatsApp.</p>

    <div class="row">
    <div class="col-md-10">
    <form method="GET" action="<?= e(url('mercado')) ?>" class="tag-box tag-box-v2">
        <?php if ($filtros['categoria_id'] > 0): ?>
            <input type="hidden" name="categoria" value="<?= e((string) $filtros['categoria_id']) ?>">
        <?php endif; ?>
        <div class="row">
            <div class="col-md-4 md-margin-bottom-20">
                <label for="f-q">Buscar</label>
                <input type="search" id="f-q" name="q" class="form-control" value="<?= e($filtros['q']) ?>" placeholder="cacao, plátano, plantas...">
            </div>
            <div class="col-md-3 md-margin-bottom-20">
                <label for="f-municipio">Municipio</label>
                <select id="f-municipio" name="municipio" class="form-control">
                    <option value="">Todos</option>
                    <?php foreach ($municipios as $m): ?>
                        <option value="<?= e((string) $m['id']) ?>" <?= $filtros['municipio_id'] === (int) $m['id'] ? 'selected' : '' ?>><?= e($m['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 md-margin-bottom-20">
                <label>&nbsp;</label>
                <div class="checkbox" style="margin: 0;">
                    <label><input type="checkbox" name="entrega" value="1" <?= $filtros['entrega'] ? 'checked' : '' ?>> Con entrega a domicilio</label>
                </div>
            </div>
            <div class="col-md-2 md-margin-bottom-20">
                <label>&nbsp;</label>
                <button type="submit" class="btn-u btn-block">FILTRAR</button>
                <?php if ($filtros['q'] !== '' || $filtros['categoria_id'] > 0 || $filtros['municipio_id'] > 0 || $filtros['entrega']): ?>
                    <a href="<?= e(url('mercado')) ?>" class="btn-u btn-limpiar btn-block">LIMPIAR</a>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <!-- Portfolio Sorting Blocks -->
    <div class="sorting-block">

        <?php if (empty($productos)): ?>
            <p class="text-center margin-bottom-40">Ningún producto coincide con la búsqueda. Si tienes un vivero, <a href="<?= e(url('mercado/vender')) ?>">publica el primero</a>.</p>
        <?php else: ?>
            <ul class="row sorting-grid">
                <?php foreach ($productos as $p): ?>
                    <li class="col-sm-6 col-xs-12">
                        <a href="<?= e(url('mercado/producto?id=' . (int) $p['id'])) ?>">
                            <?php if ($p['foto_portada']): ?>
                                <img class="img-responsive" src="<?= e(url('uploads/productos/' . basename($p['foto_portada']))) ?>" alt="<?= e($p['titulo']) ?>" loading="lazy">
                            <?php else: ?>
                                <span class="sin-foto">Sin foto</span>
                            <?php endif; ?>
                            <span class="sorting-cover">
                                <span><strong><?= e($p['titulo']) ?></strong></span>
                                <p><?= e($p['categoria']) ?></p>
                                <p>
                                    <?php if ($p['precio'] !== null): ?>
                                        <strong>$<?= e(number_format((float) $p['precio'], 2)) ?> / <?= e($p['unidad']) ?></strong>
                                    <?php else: ?>
                                        Precio a tratar
                                    <?php endif; ?>
                                    · <?= (int) $p['hace_entrega'] === 1 ? 'Entrega hasta ' . e((string) $p['entrega_km']) . ' km' : 'Recoger en el vivero' ?>
                                </p>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="clearfix"></div>
    </div>
    <!-- End Portfolio Sorting Blocks -->
    </div><!--/col-md-10-->

    <!-- Categorías (lista vertical) -->
    <div class="col-md-2">
        <div class="headline headline-md"><h2>Categorías</h2></div>
        <ul class="list-group sidebar-nav-v1 mercado-categorias">
            <li class="list-group-item <?= $filtros['categoria_id'] === 0 ? 'active' : '' ?>"><a href="<?= e($enlaceCategoria(0)) ?>">Todos <span class="badge pull-right"><?= e((string) array_sum($conteoCategorias)) ?></span></a></li>
            <?php foreach ($categorias as $c): ?>
                <li class="list-group-item <?= $filtros['categoria_id'] === (int) $c['id'] ? 'active' : '' ?>"><a href="<?= e($enlaceCategoria((int) $c['id'])) ?>"><?= e($c['nombre']) ?> <span class="badge pull-right"><?= e((string) ($conteoCategorias[(string) $c['nombre']] ?? 0)) ?></span></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    </div><!--/row-->
</div><!--/container-->
<!--=== End Content Part ===-->
