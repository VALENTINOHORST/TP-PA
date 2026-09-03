<?php

require_once __DIR__.'\includes\PageClass.php';


$body='<h4 class="text-center">Formulario de Personas</h4><br>
    
      <div class="d-flex justify-content-center">

        <form id="formPersonas" method="post" action="procesoFormulario.php" style="min-width:350px;max-width:600px;width:100%">

            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label>Nombre y Apellido</label>
                    <input type="text" class="form-control" id="nombreyape" name="nombreyape" placeholder="Nombre y Apellido">
                    <div class="valid-feedback">Buen dato!</div>
                    <div class="invalid-feedback">El campo Nombre no puede estar vacío.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Correo</label>
                    <input type="email" class="form-control" id="correo" name="correo" placeholder="algo@ejemplo.com">
                    <div class="valid-feedback">Bien!</div>
                    <div class="invalid-feedback">El campo Correo no puede estar vacío.</div>
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label>Documento</label>
                    <input type="text" class="form-control" id="documento" name="documento" value="" placeholder="Número de Documento">
                    <div class="valid-feedback">Bien!</div>
                    <div class="invalid-feedback">El campo Documento no puede estar vacío.</div>
                </div>
            </div>

            <button class="btn btn-primary" type="submit" value="">Enviar datos</button>

        </form>
    </div>';

$oPage=new PageClass();

  $oPage->setBody($body);

echo $oPage->getHtml();


?>