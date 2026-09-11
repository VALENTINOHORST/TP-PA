<?php

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
header('Location: formLogin.php');
exit;

?>