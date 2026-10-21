<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: formLogin.php');
    exit;
}

require_once __DIR__ . '/includes/PageClass.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/models/Evento.php';

$idUsuario = (int) $_SESSION['id_usuario'];
$rol = $_SESSION['rol'] ?? 'alumno';
$nombre = $_SESSION['nombre'] ?? $_SESSION['usuario'] ?? '';

$db = Database::getConexion();
$errorDb = $db ? null : Database::getErrorConexion();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$eventos = $db ? Evento::obtenerParaUsuario($idUsuario, $rol) : [];

ob_start();
require __DIR__ . '/views/eventos/index.php';
$body = ob_get_clean();

$pagina = new PageClass();
$pagina->setBody($body);

echo $pagina->getHtml();
