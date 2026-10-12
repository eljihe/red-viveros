<?php
/** @var array<int, array<string, mixed>> $productos */
/** @var array<int, array<string, mixed>> $categorias */
/** @var array<int, array<string, mixed>> $municipios */
/** @var array{q: string, categoria_id: int, municipio_id: int, entrega: bool} $filtros */
?>
<div class="sechead">
    <p class="eyebrow">Del vivero a tu casa</p>
    <h2>Productos de nuestros viveros</h2>
    <p>Plantas, frutas y cacao directo de los grupos de Sembrando Vida. Regístrate gratis para ver el teléfono y escribirles por WhatsApp.</p>
    <div class="actions" style="justify-content: center; margin-top: 14px;">
        <a href="<?= e(url('mercado/vender')) ?>" class="btn pri big">+ Vender un producto</a>
    </div>
</div>

<form method="GET" action="<?= e(url('mercado')) ?>" class="filters boxed">
    <div class="field" style="flex: 1 1 200px;">
        <span>Buscar</span>
        <input type="search" name="q" value="<?= e($filtros['q']) ?>" placeholder="cacao, plátano, plantas...">
    </div>
    <div class="field">
        <span>Categoría</span>
        <select name="categoria">
            <option value="">Todas</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= e((string) $c['id']) ?>" <?= $filtros['categoria_id'] === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="field">
        <span>Municipio</span>
        <select name="municipio">
            <option value="">Todos</option>
            <?php foreach ($municipios as $m): ?>
                <option value="<?= e((string) $m['id']) ?>" <?= $filtros['municipio_id'] === (int) $m['id'] ? 'selected' : '' ?>><?= e($m['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <label class="toggle">
        <input type="checkbox" name="entrega" value="1" <?= $filtros['entrega'] ? 'checked' : '' ?>>
        Con entrega a domicilio
    </label>
    <button type="submit" class="btn pri">Filtrar</button>
</form>

<?php if (empty($productos)): ?>
    <p class="empty" style="margin-top: 16px;">Ningún producto coincide con la búsqueda. Si tienes un vivero, <a href="<?= e(url('mercado/vender')) ?>">publica el primero</a>.</p>
<?php else: ?>
    <div class="grid" style="margin-top: 16px;">
        <?php foreach ($productos as $p): ?>
            <a href="<?= e(url('mercado/producto?id=' . (int) $p['id'])) ?>" class="card">
                <?php if ($p['foto_portada']): ?>
                    <img class="photo" src="<?= e(url('uploads/productos/' . basename($p['foto_portada']))) ?>" alt="<?= e($p['titulo']) ?>">
                <?php else: ?>
                    <div class="ph">Sin foto</div>
                <?php endif; ?>
                <div class="body">
                    <span class="tag"><?= e($p['categoria']) ?></span>
                    <h4><?= e($p['titulo']) ?></h4>
                    <div class="who"><?= e($p['vivero_nombre']) ?> · <?= e($p['comunidad']) ?></div>
                    <?php if ($p['precio'] !== null): ?>
                        <div class="price">$<?= e(number_format((float) $p['precio'], 2)) ?> <small>/ <?= e($p['unidad']) ?></small></div>
                    <?php else: ?>
                        <div class="hint">Precio a tratar</div>
                    <?php endif; ?>
                    <?php if ((int) $p['hace_entrega'] === 1): ?>
                        <span class="deliv">Entrega hasta <?= e((string) $p['entrega_km']) ?> km</span>
                    <?php else: ?>
                        <span class="hint">Recoger en el vivero</span>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
