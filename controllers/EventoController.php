<?php

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../models/Evento.php';

class EventoController
{
    public static function listarPublicos(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!Database::getConexion()) {
            http_response_code(503);
            echo json_encode([
                'error' => Database::getErrorConexion()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $eventos = Evento::obtenerPublicos();
        $eventosTimeline = [];

        foreach ($eventos as $evento) {
            $eventosTimeline[] = [
                'title' => $evento['titulo'],
                'subtitle' => $evento['materia'] . ' (' . $evento['tipo'] . ')',
                'date' => $evento['fecha_evento']
            ];
        }

        echo json_encode($eventosTimeline, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function procesar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_usuario'])) {
            header('Location: formLogin.php');
            exit;
        }

        $idUsuario = (int) $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'] ?? 'alumno';

        if (!Database::getConexion()) {
            $_SESSION['flash'] = [
                'tipo' => 'danger',
                'mensaje' => Database::getErrorConexion()
            ];

            header('Location: Inicio.php');
            exit;
        }

        $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

        switch ($accion) {
            case 'crear':
                $datos = self::obtenerDatosEvento();
                $datos['es_global'] = ($rol === 'admin' && !empty($_POST['es_global'])) ? 1 : 0;

                if (!self::datosValidos($datos)) {
                    self::redirigir('warning', 'Por favor, complete todos los campos obligatorios con valores válidos.');
                }

                $exito = Evento::crear($datos, $idUsuario, $rol);

                self::redirigir(
                    $exito ? 'success' : 'danger',
                    $exito
                        ? '¡Fecha / Evento registrado correctamente!'
                        : 'Ocurrió un error al intentar registrar el evento.'
                );

            case 'editar':
                $id = (int) ($_POST['id_evento'] ?? 0);
                $datos = self::obtenerDatosEvento();
                $datos['es_global'] = ($rol === 'admin' && !empty($_POST['es_global'])) ? 1 : 0;

                if ($id <= 0 || !self::datosValidos($datos)) {
                    self::redirigir('warning', 'Datos inválidos para modificar el evento.');
                }

                $exito = Evento::actualizar($id, $datos, $idUsuario, $rol);

                self::redirigir(
                    $exito ? 'success' : 'danger',
                    $exito
                        ? '¡Fecha / Evento modificado exitosamente!'
                        : 'No se pudo modificar el evento o no posee los permisos necesarios.'
                );

            case 'eliminar':
                $id = (int) ($_POST['id_evento'] ?? $_GET['id'] ?? 0);

                if ($id <= 0) {
                    self::redirigir('warning', 'Identificador de evento inválido.');
                }

                $exito = Evento::eliminar($id, $idUsuario, $rol);

                self::redirigir(
                    $exito ? 'success' : 'danger',
                    $exito
                        ? '¡Fecha / Evento eliminado correctamente!'
                        : 'No se pudo eliminar el evento o no posee los permisos necesarios.'
                );

            default:
                header('Location: Inicio.php');
                exit;
        }
    }

    private static function obtenerDatosEvento(): array
    {
        return [
            'titulo' => trim($_POST['titulo'] ?? ''),
            'materia' => trim($_POST['materia'] ?? ''),
            'tipo' => trim($_POST['tipo'] ?? ''),
            'fecha_evento' => trim($_POST['fecha_evento'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? '')
        ];
    }

    private static function datosValidos(array $datos): bool
    {
        $tiposValidos = ['Examen', 'Entrega', 'Parcial', 'Final'];

        return $datos['titulo'] !== ''
            && $datos['materia'] !== ''
            && in_array($datos['tipo'], $tiposValidos, true)
            && $datos['fecha_evento'] !== '';
    }

    private static function redirigir(string $tipo, string $mensaje): void
    {
        $_SESSION['flash'] = [
            'tipo' => $tipo,
            'mensaje' => $mensaje
        ];

        header('Location: Inicio.php');
        exit;
    }
}