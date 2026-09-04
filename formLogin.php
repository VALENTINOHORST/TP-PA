<?php

require_once __DIR__.'/includes/PageClass.php';


$body='<h4 class="text-center">Formulario de Login</h4><br>

    <div class="d-flex justify-content-center">

        <form id="formLogin" method="post" action="procesoLogin.php" style="min-width:350px;max-width:400px;width:100%">

            <div class="mb-3">
                <label>Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Usuario" autocomplete="off">
                <div class="valid-feedback">Bien!</div>
                <div class="invalid-feedback">El campo Usuario no puede estar vacío.</div>
            </div>

            <div class="mb-3">
                <label>Contraseña</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="Contraseña">
                <div class="valid-feedback">Bien!</div>
                <div class="invalid-feedback">El campo Contraseña no puede estar vacío.</div>
            </div>

            <button class="btn btn-primary" type="submit" id="btnLogin" disabled>Ingresar</button>

            <div id="loginMensaje" class="mt-3"></div>

        </form>
    </div>';

$oPage=new PageClass();

  $oPage->setBody($body);

echo $oPage->getHtml();


?>