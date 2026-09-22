<?php

require_once __DIR__.'/includes/PageClass.php';

$usuarioValido    = 'fcytuader';
$contrasenaValida = 'programacionavanzada';

$ok      = false;
$mensaje = 'Debe completar todos los campos.';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$esAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

if (isset($_POST['usuario']) && isset($_POST['contrasena']) && isset($_POST['captcha'])) {
    if (!isset($_SESSION['captcha']) || $_POST['captcha'] !== $_SESSION['captcha']) {
        $mensaje = 'El código CAPTCHA es incorrecto.';
    } else if ($_POST['usuario'] === $usuarioValido && $_POST['contrasena'] === $contrasenaValida) {
        $ok      = true;
        $mensaje = 'Ingresó correctamente!';
        $_SESSION['usuario'] = $_POST['usuario'];
        
        if (!$esAjax) {
            unset($_SESSION['captcha']);
        }
    } else {
        $mensaje = 'Usuario o contraseña incorrectos.';
    }
}

if ($esAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => $ok, 'mensaje' => $mensaje]);
    exit;
}

if ($ok) {
    $cuerpoMensaje = '<div class="alert alert-success text-center" role="alert">'.$mensaje.'</div>';
} else {
    $cuerpoMensaje = '<div class="alert alert-danger text-center" role="alert">'.$mensaje.'</div>
                      <div class="d-flex justify-content-center">
                          <a href="formLogin.php" class="btn btn-secondary">Volver al formulario</a>
                      </div>';
}

$body='<h4 class="text-center">Resultado del Login</h4><br>
        '.$cuerpoMensaje;

$oPage=new PageClass();

  $oPage->setBody($body);

echo $oPage->getHtml();

?>