<?php

require_once __DIR__ . '/includes/PageClass.php';

$body = '
<div class="row my-4 text-center">
<div class="col-12">
<h2 class="fw-bold">Calendario Académico Oficial</h2>
<p class="text-muted">Cronograma público de exámenes, parciales y entregas registrado en el sistema.</p>
<div class="d-flex justify-content-center gap-2 mb-3">
<span class="badge bg-danger">Finales</span>
<span class="badge bg-warning text-dark">Parciales</span>
<span class="badge bg-primary">Exámenes</span>
<span class="badge bg-success">Entregas</span>
</div>
</div>
</div>

<!-- Contenedor donde CalendarJS renderiza las fechas de la BD -->
<div class="card shadow-sm mb-5">
<div class="card-body p-3">
<div id="calendario" style="min-height: 600px;"></div>
</div>
</div>
';

$oPage = new PageClass();
$oPage->setBody($body);

echo $oPage->getHtml();
