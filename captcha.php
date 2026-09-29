<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$codigo = '';
for ($i = 0; $i < 5; $i++) {
    $codigo .= rand(0, 9);
}

$_SESSION['captcha'] = $codigo;


$ancho = 120;
$alto = 40;
$imagen = imagecreate($ancho, $alto);


$colorFondo = imagecolorallocate($imagen, 230, 230, 230);
$colorTexto = imagecolorallocate($imagen, 20, 20, 20);
$colorLineas = imagecolorallocate($imagen, 150, 150, 150);
$colorPuntos = imagecolorallocate($imagen, 120, 120, 120);


for($i=0; $i < 15; $i++) {
    imageline($imagen, rand(0, $ancho), rand(0, $alto), rand(0, $ancho), rand(0, $alto), $colorLineas);
}


for($i=0; $i < 100; $i++) {
    imagesetpixel($imagen, rand(0, $ancho), rand(0, $alto), $colorPuntos);
}


$espacio = $ancho / 5;
for ($i = 0; $i < 5; $i++) {
    $x = ($i * $espacio) + 8;
    $y = rand(8, 18);
    imagestring($imagen, 5, $x, $y, $codigo[$i], $colorTexto);
}

// Enviar cabeceras e imprimir la imagen
header('Content-Type: image/png');
header('Cache-Control: no-cache, no-store, must-revalidate'); 
header('Pragma: no-cache'); 
header('Expires: 0'); 

imagepng($imagen);
imagedestroy($imagen);
?>

