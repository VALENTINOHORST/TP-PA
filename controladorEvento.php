<?php

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Evento.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Proteger el controlador: sólo usuarios autenticados
if (!isset($_SESSION['id_usuario'])) {
    header('Location: formLogin.php');
    exit;
}

$idUsuario = (int)$_SESSION['id_usuario'];
$rol       = $_SESSION['rol'] ?? 'alumno';

// 2. Validar conexión a la Base de Datos
$db = Database::getConexion();
if (!$db) {
    $_SESSION['flash'] = [
        'tipo'    => 'danger',
        'mensaje' => Database::getErrorConexion()
    ];
    header('Location: Inicio.php');
    exit;
}

// 3. Determinar la acción solicitada
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {
    case 'crear':
        $titulo       = trim($_POST['titulo'] ?? '');
        $materia      = trim($_POST['materia'] ?? '');
        $tipo         = trim($_POST['tipo'] ?? '');
        $fecha_evento = trim($_POST['fecha_evento'] ?? '');
        $descripcion  = trim($_POST['descripcion'] ?? '');
        $esGlobal     = ($rol === 'admin' && !empty($_POST['es_global'])) ? 1 : 0;

        $tiposValidos = ['Examen', 'Entrega', 'Parcial', 'Final'];

        if ($titulo === '' || $materia === '' || !in_array($tipo, $tiposValidos, true) || $fecha_evento === '') {
            $_SESSION['flash'] = [
                'tipo'    => 'warning',
                'mensaje' => 'Por favor, complete todos los campos obligatorios con valores válidos.'
            ];
            header('Location: Inicio.php');
            exit;
        }

        $exito = Evento::crear([
            'titulo'       => $titulo,
            'materia'      => $materia,
            'tipo'         => $tipo,
            'fecha_evento' => $fecha_evento,
            'descripcion'  => $descripcion,
            'es_global'    => $esGlobal
        ], $idUsuario, $rol);

        if ($exito) {
            $_SESSION['flash'] = [
                'tipo'    => 'success',
                'mensaje' => '¡Fecha / Evento registrado correctamente!'
            ];
        } else {
            $_SESSION['flash'] = [
                'tipo'    => 'danger',
                'mensaje' => 'Ocurrió un error al intentar registrar el evento.'
            ];
        }
        header('Location: Inicio.php');
        exit;

    case 'editar':
        $id           = (int)($_POST['id_evento'] ?? 0);
        $titulo       = trim($_POST['titulo'] ?? '');
        $materia      = trim($_POST['materia'] ?? '');
        $tipo         = trim($_POST['tipo'] ?? '');
        $fecha_evento = trim($_POST['fecha_evento'] ?? '');
        $descripcion  = trim($_POST['descripcion'] ?? '');
        $esGlobal     = ($rol === 'admin' && !empty($_POST['es_global'])) ? 1 : 0;

        $tiposValidos = ['Examen', 'Entrega', 'Parcial', 'Final'];

        if ($id <= 0 || $titulo === '' || $materia === '' || !in_array($tipo, $tiposValidos, true) || $fecha_evento === '') {
            $_SESSION['flash'] = [
                'tipo'    => 'warning',
                'mensaje' => 'Datos inválidos para modificar el evento.'
            ];
            header('Location: Inicio.php');
            exit;
        }

        $exito = Evento::actualizar($id, [
            'titulo'       => $titulo,
            'materia'      => $materia,
            'tipo'         => $tipo,
            'fecha_evento' => $fecha_evento,
            'descripcion'  => $descripcion,
            'es_global'    => $esGlobal
        ], $idUsuario, $rol);

        if ($exito) {
            $_SESSION['flash'] = [
                'tipo'    => 'success',
                'mensaje' => '¡Fecha / Evento modificado exitosamente!'
            ];
        } else {
            $_SESSION['flash'] = [
                'tipo'    => 'danger',
                'mensaje' => 'No se pudo modificar el evento o no posee los permisos necesarios.'
            ];
        }
        header('Location: Inicio.php');
        exit;

    case 'eliminar':
        $id = (int)($_POST['id_evento'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash'] = [
                'tipo'    => 'warning',
                'mensaje' => 'Identificador de evento inválido.'
            ];
            header('Location: Inicio.php');
            exit;
        }

        $exito = Evento::eliminar($id, $idUsuario, $rol);

        if ($exito) {
            $_SESSION['flash'] = [
                'tipo'    => 'success',
                'mensaje' => '¡Fecha / Evento eliminado correctamente!'
            ];
        } else {
            $_SESSION['flash'] = [
                'tipo'    => 'danger',
                'mensaje' => 'No se pudo eliminar el evento o no posee los permisos necesarios.'
            ];
        }
        header('Location: Inicio.php');
        exit;

    default:
        header('Location: Inicio.php');
        exit;
}

