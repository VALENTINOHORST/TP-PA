<?php

require_once __DIR__.'/includes/PageClass.php';

$body='<h4 class="text-center">Bienvenido, Para empezar inicie sesión</h4>';

    $oPage=new PageClass();

      $oPage->setBody($body);

    echo $oPage->getHtml();


?>
