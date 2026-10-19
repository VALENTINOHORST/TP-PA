<?php

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Evento.php';

header('Content-Type: application/json; charset=utf-8');

// 1. Validar conexión a la Base de Datos
$db = Database::getConexion();
if (!$db) {
    http_response_code(503);
    echo json_encode([
        'error' => Database::getErrorConexion()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2. Obtener fechas oficiales cargadas en MariaDB
$listaBD = Evento::obtenerPublicos();

// 3. Formatear para el Timeline de CalendarJS
$eventosTimeline = [];
foreach ($listaBD as $e) {
    $eventosTimeline[] = [
        'title'    => $e['titulo'],
        'subtitle' => $e['materia'] . ' (' . $e['tipo'] . ')',
        'date'     => $e['fecha_evento'], // Formato YYYY-MM-DD
    ];
}

echo json_encode($eventosTimeline, JSON_UNESCAPED_UNICODE);
exit;
