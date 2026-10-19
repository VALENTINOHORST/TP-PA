<?php

require_once __DIR__ . '/includes/PageClass.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Usuario.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$esAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

$ok = false;
$mensaje = 'Debe completar todos los campos.';

// 1. Validar conexión a la Base de Datos (captura de fallo amigable)
$db = Database::getConexion();
if (!$db) {
    $mensaje = Database::getErrorConexion();

    if ($esAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'mensaje' => $mensaje]);
        exit;
    }

    header('Location: formLogin.php?error=' . urlencode($mensaje));
    exit;
}

// 2. Procesar datos del formulario
if (isset($_POST['usuario']) && isset($_POST['contrasena']) && isset($_POST['captcha'])) {
    $usuarioInput    = trim($_POST['usuario']);
    $contrasenaInput = $_POST['contrasena'];
    $captchaInput    = trim($_POST['captcha']);

    // Validar captcha almacenado en sesión
    if (!isset($_SESSION['captcha']) || $captchaInput !== $_SESSION['captcha']) {
        $mensaje = 'El código CAPTCHA es incorrecto.';
    } else {
        // Autenticación con la Base de Datos
        $usuario = Usuario::autenticar($usuarioInput, $contrasenaInput);

        if ($usuario) {
            $ok = true;
            $mensaje = 'Ingresó correctamente!';

            // Guardar datos del usuario en la sesión
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['usuario']    = $usuario['usuario'];
            $_SESSION['nombre']     = $usuario['nombre'];
            $_SESSION['rol']        = $usuario['rol'];

            // Limpiar captcha en el flujo no-AJAX
            if (!$esAjax) {
                unset($_SESSION['captcha']);
            }
        } else {
            $mensaje = 'Usuario o contraseña incorrectos.';
        }
    }
}

// 3. Responder según el origen de la solicitud
if ($esAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => $ok, 'mensaje' => $mensaje]);
    exit;
}

if ($ok) {
    header('Location: Inicio.php');
    exit;
} else {
    header('Location: formLogin.php?error=' . urlencode($mensaje));
    exit;
}
