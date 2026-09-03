<?php
require_once __DIR__.'\includes\PageClass.php';

if (isset($_POST['nombreyape']) && isset($_POST['correo']) && isset($_POST['documento'])) {

    $filas="";
    foreach ( $_POST as $indice => $valor ) {
        $filas.='<tr>
                    <td>'.$indice.'</td>
                    <td>'.$valor.'</td>
                </tr>';
    }
    
}
$body='<h4 class="text-center">Datos Recibidos del Formulario</h4>
        <h6 class="text-center">Se reciben los datos desde el array $_POST</h6>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Indice</th>
                <th>Valor</th>
            </tr>
            </thead>
            <tbody>
            '.$filas.'
            </tbody>
        </table>';

$oPage=new PageClass();

  $oPage->setBody($body);

echo $oPage->getHtml();
