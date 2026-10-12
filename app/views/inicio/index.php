<?php
/** @var int $municipios */
/** @var int $especies */
/** @var string|null $error */
?>
<h2>Diagnóstico de conexión</h2>
<?php if ($error !== null): ?>
    <div class="panel">
        <p>No fue posible consultar la base de datos.</p>
        <p><code><?= e($error) ?></code></p>
    </div>
<?php else: ?>
    <div class="panel">
        <p>Municipios registrados: <strong><?= e((string) $municipios) ?></strong></p>
        <p>Especies registradas: <strong><?= e((string) $especies) ?></strong></p>
    </div>
<?php endif; ?>
