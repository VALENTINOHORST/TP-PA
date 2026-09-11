<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header('Location: formLogin.php');
    exit;
}
require_once __DIR__.'/includes/PageClass.php';


$body='<h4 class="text-center">Si llegaste aca, tendrias que estar logueado</h4>';

    $oPage=new PageClass();

      $oPage->setBody($body);

    echo $oPage->getHtml();


?>
