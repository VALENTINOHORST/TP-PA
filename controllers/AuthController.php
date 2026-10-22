<?php

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    public static function cerrarSesion(): void
    {
        if (session_status() === PHP_SESSION_NONE && isset($_COOKIE[session_name()])) {
            session_start();
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $parametrosCookie = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $parametrosCookie['path'],
                    $parametrosCookie['domain'],
                    $parametrosCookie['secure'],
                    $parametrosCookie['httponly']
                );
            }

            session_destroy();
        }

        header('Location: index.php?opt=login');
        exit;
    }

    public static function procesarLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $esAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

        $ok = false;
        $mensaje = 'Debe completar todos los campos.';

        if (!Database::getConexion()) {
            $mensaje = Database::getErrorConexion();

            if ($esAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'mensaje' => $mensaje]);
                exit;
            }

            header('Location: index.php?opt=login&error=' . urlencode($mensaje));
            exit;
        }

        if (
            isset($_POST['usuario'])
            && isset($_POST['contrasena'])
            && isset($_POST['captcha'])
        ) {
            $usuarioInput = trim($_POST['usuario']);
            $contrasenaInput = $_POST['contrasena'];
            $captchaInput = trim($_POST['captcha']);

            if (!isset($_SESSION['captcha']) || $captchaInput !== $_SESSION['captcha']) {
                $mensaje = 'El código CAPTCHA es incorrecto.';
            } else {
                $usuario = Usuario::autenticar($usuarioInput, $contrasenaInput);

                if ($usuario) {
                    $ok = true;
                    $mensaje = 'Ingresó correctamente!';

                    $_SESSION['id_usuario'] = $usuario['id'];
                    $_SESSION['usuario'] = $usuario['usuario'];
                    $_SESSION['nombre'] = $usuario['nombre'];
                    $_SESSION['rol'] = $usuario['rol'];

                    unset($_SESSION['captcha']);
                } else {
                    $mensaje = 'Usuario o contraseña incorrectos.';
                }
            }
        }

        if ($esAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => $ok, 'mensaje' => $mensaje]);
            exit;
        }

        if ($ok) {
            header('Location: index.php?opt=inicio');
            exit;
        }

        header('Location: index.php?opt=login&error=' . urlencode($mensaje));
        exit;
    }
}
