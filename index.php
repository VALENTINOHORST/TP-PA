<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/PageClass.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/models/Evento.php';
require_once __DIR__ . '/models/Usuario.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/EventoController.php';

// Si se accede a index.php sin ?opt= en la URL, redirigir explícitamente para que ?opt= siempre se vea en la barra de direcciones
if (!isset($_GET['opt']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['id_usuario'])) {
        header('Location: index.php?opt=inicio');
    } else {
        header('Location: index.php?opt=publico');
    }
    exit;
}

// Capturar el parámetro de la acción o vista solicitada
$opt = $_GET['opt'] ?? $_POST['opt'] ?? 'publico';

switch ($opt) {
    case 'publico':
        // Vista pública con Timeline de CalendarJS
        $cuerpoPagina = file_get_contents(__DIR__ . '/views/eventos/publico.php');
        if ($cuerpoPagina === false) {
            throw new RuntimeException('No se pudo cargar la vista pública del calendario.');
        }

        $pagina = new PageClass();
        $pagina->setBody($cuerpoPagina);
        echo $pagina->getHtml();
        break;

    case 'login':
        // Si el usuario ya está autenticado, lo redirigimos al panel de gestión
        if (isset($_SESSION['id_usuario'])) {
            header('Location: index.php?opt=inicio');
            exit;
        }

        $mostrarError = isset($_GET['error']);
        $error = $_GET['error'] ?? '';

        ob_start();
        require __DIR__ . '/views/auth/login.php';
        $body = ob_get_clean();

        $pagina = new PageClass();
        $pagina->setBody($body);
        echo $pagina->getHtml();
        break;

    case 'procesoLogin':
        // Procesa el login (POST)
        AuthController::procesarLogin();
        break;

    case 'inicio':
    case 'mis-fechas':
        // Vista del ABM con DataTables (requiere autenticación)
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: index.php?opt=login');
            exit;
        }

        $idUsuario = (int) $_SESSION['id_usuario'];
        $rol       = $_SESSION['rol'] ?? 'alumno';
        $nombre    = $_SESSION['nombre'] ?? $_SESSION['usuario'] ?? '';

        $db = Database::getConexion();
        $errorDb = $db ? null : Database::getErrorConexion();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $eventos = $db ? Evento::obtenerParaUsuario($idUsuario, $rol) : [];

        ob_start();
        require __DIR__ . '/views/eventos/index.php';
        $body = ob_get_clean();

        $pagina = new PageClass();
        $pagina->setBody($body);
        echo $pagina->getHtml();
        break;

    case 'evento':
        // Procesa el ABM (Alta, Baja y Modificación)
        EventoController::procesar();
        break;

    case 'logout':
        // Cierra la sesión
        AuthController::cerrarSesion();
        break;

    case 'api_eventos':
        // Endpoint JSON para CalendarJS
        EventoController::listarPublicos();
        break;

    default:
        // Opción no reconocida: redirigir a la portada pública explícitamente
        header('Location: index.php?opt=publico');
        exit;
}
