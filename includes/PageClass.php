<?php

class PageClass
{
    private $header;
    private $navbar;
    private $body;
    private $js;
    private $footer;


    function __construct()
    {
       $this->setHeader();
       $this->setNavBar();
       $this->setJs();
       $this->setFooter();

    }


    private function setHeader()
    {
        $this->header='<!DOCTYPE html>
                <html lang="es">
                 <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                    <title>Programación Avanzada</title>
                    <link rel="icon" href="/imgs/favicon.ico" type="image/x-icon">
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
                    <link href="css/estilos.css" rel="stylesheet">
                </head>
                <body class="bg-light">
                    <div class="container">';
    }


    private function setNavBar()
    {
        $this->navbar='<nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="index.php">
                            <img src="imgs/desarrollo-web.svg" width="30" height="30" class="d-inline-block align-top">
                            Programación Avanzada
                        </a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page" href="index.php">Inicio</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" aria-current="page" href="formLogin.php">Login</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>';
    }


    public function setBody($body)
    {
        $this->body=$body;
        $this->body.='</div>';
    }


    private function setJs()
    {
        $this->js='<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
                   <script src="js/formPersonas.js"></script>
                   <script src="js/formLogin.js"></script>';
    }

    private function setFooter()
    {
        $this->footer='<footer class="text-center py-3 my-4 border-top text-muted">
                        <small>Programación Avanzada &copy; 2026 - FCyT UADER</small>
                       </footer>
                       </body></html>';
    }

    public function getHtml()
    {
        $Pagina=$this->header;
        $Pagina.=$this->navbar;
        $Pagina.=$this->body;
        $Pagina.=$this->js;
        $Pagina.=$this->footer;
        return $Pagina;
    }


}