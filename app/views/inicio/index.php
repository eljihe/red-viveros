<?php
/** @var array<int, array<string, mixed>> $municipios */
/** @var array<int, array<string, mixed>> $especies */
/** @var array<int, array<string, mixed>> $viveros */
/** @var string|null $error */

$totalPlantas = 0;
foreach ($viveros as $v) {
    foreach ($v['inventario'] as $fila) {
        $totalPlantas += (int) $fila['cantidad'];
    }
}

$datosMapa = array_map(static function (array $v): array {
    return [
        'id' => (int) $v['id'],
        'nombre' => $v['nombre'],
        'historia' => $v['historia'],
        'lat' => (float) $v['lat'],
        'lng' => (float) $v['lng'],
        'integrantes' => $v['integrantes'] !== null ? (int) $v['integrantes'] : null,
        'comunidad' => $v['comunidad'],
        'municipio' => $v['municipio'],
        'municipio_id' => (int) $v['municipio_id'],
        'fotos' => array_values(array_map(
            static fn (string $archivo): string => url('uploads/viveros/' . basename($archivo)),
            $v['fotos']
        )),
        'inventario' => array_values(array_map(static function (array $fila): array {
            return [
                'especie_id' => (int) $fila['especie_id'],
                'especie_nombre' => $fila['especie_nombre'],
                'cantidad' => (int) $fila['cantidad'],
                'edad_meses' => $fila['edad_meses'] !== null ? (int) $fila['edad_meses'] : null,
            ];
        }, $v['inventario'])),
    ];
}, $viveros);
?>
<div class="sechead">
    <p class="eyebrow">Red de Viveros Tabasco</p>
    <p>Plataforma de venta directa de plantas de viveros comunitarios de Sembrando Vida.</p>
</div>

<?php if ($error !== null): ?>
    <div class="msg err"><?= e($error) ?></div>
<?php else: ?>
    <div class="stats">
        <div class="stat"><span class="n"><?= e((string) count($viveros)) ?></span><span class="l">viveros en el mapa</span></div>
        <div class="stat"><span class="n"><?= e((string) $totalPlantas) ?></span><span class="l">plantas disponibles</span></div>
        <div class="stat"><span class="n"><?= e((string) count($especies)) ?></span><span class="l">especies</span></div>
    </div>

    <?php if (empty($viveros)): ?>
        <div class="msg">
            Todavía no hay viveros aprobados en el mapa.
            Si tienes un vivero, <a href="<?= e(url('registro/vivero')) ?>">regístralo aquí</a>.
        </div>
    <?php endif; ?>

    <div class="filters">
        <div class="field" style="min-width: 160px;">
            <span>Busco planta</span>
            <select id="filtro-especie">
                <option value="">Todas</option>
                <?php foreach ($especies as $especie): ?>
                    <option value="<?= e((string) $especie['id']) ?>"><?= e($especie['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field" style="min-width: 160px;">
            <span>Municipio</span>
            <select id="filtro-municipio">
                <option value="">Todos</option>
                <?php foreach ($municipios as $municipio): ?>
                    <option value="<?= e((string) $municipio['id']) ?>"><?= e($municipio['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <p id="resultado-filtro" class="msg" aria-live="polite" hidden></p>

    <div class="mapgrid">
        <div class="mapbox">
            <div id="mapa-publico" style="height: 420px;"></div>
        </div>
        <div class="side">
            <div id="detalle-vivero" class="panel">
                <h3>Elige un vivero</h3>
                <p class="hint">Toca un marcador del mapa o un nombre de la lista para ver sus plantas y cómo llegar.</p>
            </div>
            <div id="lista-viveros" class="vlist"></div>
        </div>
    </div>

    <script type="application/json" id="datos-mapa"><?= json_encode(
        $datosMapa,
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
    ) ?></script>
<?php endif; ?>

<div class="actions" style="justify-content: center; gap: 15px; margin: 30px 0 10px;">
    <a href="<?= e(url('registro')) ?>" class="btn pri" style="min-width: 150px;">Registrarme</a>
    <a href="<?= e(url('cuenta')) ?>" class="btn" style="min-width: 150px;">Mi cuenta</a>
</div>
<p style="text-align: center; color: var(--muted); font-size: 0.9rem;">
    <a href="<?= e(url('mercado')) ?>" style="color: inherit;">Mercado</a>
</p>
