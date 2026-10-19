<?php

if (session_status() === PHP_SESSION_NONE && isset($_COOKIE[session_name()])) {
    session_start();
}

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
        $this->header = '<!DOCTYPE html>
        <html lang="es">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Programación Avanzada</title>
        <link rel="icon" href="/imgs/favicon.ico" type="image/x-icon">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <!-- Iconos y Estilos de CalendarJS -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@calendarjs/ce/dist/style.min.css">
        <link href="css/estilos.css" rel="stylesheet">
        </head>
        <body class="bg-light d-flex flex-column min-vh-100">
        <div class="container">';
    }

    private function setNavBar()
    {
        if (isset($_SESSION['usuario']) && !empty($_SESSION['usuario'])) {
            $usuario = htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8');
            $enlaceSesion = '
            <div class="d-flex align-items-center">
            <span class="navbar-text me-3">
            Logueado como <strong class="text-dark">' . $usuario . '</strong>
            </span>
            <a class="btn btn-outline-danger btn-sm" href="logout.php">Cerrar sesión</a>
            </div>';
        } else {
            $enlaceSesion = '
            <ul class="navbar-nav">
            <li class="nav-item">
            <a class="nav-link" href="formLogin.php">Login</a>
            </li>
            </ul>';
        }

        $this->navbar = '<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
        <img src="imgs/desarrollo-web.svg" width="30" height="30" class="d-inline-block align-top">
        Calendario Academico
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
        <a class="nav-link" href="index.php">Público</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="Inicio.php">Mis Fechas</a>
        </li>
        </ul>
        ' . $enlaceSesion . '
        </div>
        </div>
        </nav>';
    }

    public function setBody($body)
    {
        $this->body = $body;
        $this->body .= '</div>';
    }

    private function setJs()
    {
        $this->js = '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
        <!-- Dependencias de CalendarJS -->
        <script src="https://cdn.jsdelivr.net/npm/lemonadejs/dist/lemonade.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@calendarjs/ce/dist/index.min.js"></script>
        <!-- Scripts locales -->
        <script src="js/formLogin.js"></script>
        <script src="js/calendario.js"></script>';
    }

    private function setFooter()
    {
        $this->footer = '<footer class="text-center py-3 mt-auto bg-light border-top text-muted">
        <small>Programación Avanzada &copy; 2026 - FCyT UADER</small>
        </footer>
        </body></html>';
    }

    public function getHtml()
    {
        $Pagina  = $this->header;
        $Pagina .= $this->navbar;
        $Pagina .= $this->body;
        $Pagina .= $this->js;
        $Pagina .= $this->footer;
        return $Pagina;
    }
}
