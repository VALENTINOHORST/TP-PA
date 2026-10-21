<?php

require_once __DIR__ . '/includes/PageClass.php';

$mostrarError = isset($_GET['error']);
$error = $_GET['error'] ?? '';

ob_start();
require __DIR__ . '/views/auth/login.php';
$body = ob_get_clean();

$pagina = new PageClass();
$pagina->setBody($body);

echo $pagina->getHtml();
