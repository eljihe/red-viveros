<?php
/** @var array<int, array<string, mixed>> $municipios */
/** @var array<int, array<string, mixed>> $especies */
/** @var array<int, array<string, mixed>> $viveros */
/** @var string|null $error */


$datosMapa = array_map(static function (array $v): array {
    return [
        'id' => (int) $v['id'],
        'url' => url('vivero/ficha?id=' . (int) $v['id']),
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
        'servicios' => array_values(array_map(static function (array $fila): array {
            return [
                'titulo' => $fila['titulo'],
                'descripcion' => $fila['descripcion'],
                'horario' => $fila['horario'],
                'costo' => $fila['costo'],
            ];
        }, $v['servicios'])),
    ];
}, $viveros);
?>
<!--=== Avisos ===-->
<?php if ($error !== null || empty($viveros)): ?>
<div class="container margin-top-20">
    <?php if ($error !== null): ?>
        <div class="tag-box tag-box-v2"><p><?= e($error) ?></p></div>
    <?php else: ?>
        <div class="tag-box tag-box-v2">
            <p>Todavía no hay viveros aprobados en el mapa. Si tienes un vivero, <a href="<?= e(url('registro/vivero')) ?>">regístralo aquí</a>.</p>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!--=== Mapa a todo el ancho ===-->
<?php if ($error === null): ?>
<div class="mapa-caja mapa-completo">
    <div id="mapa-publico"></div>
    <div class="mapa-pie"><p>Toca un marcador del mapa o un nombre de la lista para ver la ficha del vivero.</p></div>
</div>

<!--=== Filtros (debajo del mapa) ===-->
<div class="container margin-top-20">
    <div class="tag-box tag-box-v2">
        <div class="row">
            <div class="col-md-6 md-margin-bottom-20">
                <label for="filtro-especie">Busco planta</label>
                <select id="filtro-especie" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach ($especies as $especie): ?>
                        <option value="<?= e((string) $especie['id']) ?>"><?= e($especie['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="filtro-municipio">Municipio</label>
                <select id="filtro-municipio" class="form-control">
                    <option value="">Todos</option>
                    <?php foreach ($municipios as $municipio): ?>
                        <option value="<?= e((string) $municipio['id']) ?>"><?= e($municipio['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <p id="resultado-filtro" class="msg" aria-live="polite" hidden></p>
</div>
<?php endif; ?>
<!--=== End Mapa y filtros ===-->

<!--=== Content Part ===-->
<div class="container">
    <?php if ($error === null): ?>
        <!-- Lista de viveros (tarjetas) -->
        <div class="headline"><h2>Viveros <span class="vivero-total"><span id="total-viveros"><?= e((string) count($viveros)) ?></span></span></h2></div>
        <div id="lista-viveros" class="row servive-block lista-viveros"></div>
        <!-- End Lista -->

        <script type="application/json" id="datos-mapa"><?= json_encode(
            $datosMapa,
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
        ) ?></script>
    <?php endif; ?>
</div><!--/container-->
<!--=== End Content Part ===-->
