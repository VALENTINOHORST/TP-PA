<?php

require_once __DIR__ . '/includes/PageClass.php';

$cuerpoPagina = file_get_contents(__DIR__ . '/views/eventos/publico.php');
if ($cuerpoPagina === false) {
    throw new RuntimeException('No se pudo cargar la vista pública del calendario.');
}

$pagina = new PageClass();
$pagina->setBody($cuerpoPagina);

echo $pagina->getHtml();
