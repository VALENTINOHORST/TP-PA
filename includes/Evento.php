<?php

require_once __DIR__ . '/Database.php';

class Evento
{
    /**
     * Consulta las fechas públicas (es_global = 1) para usuarios no logueados.
     */
    public static function obtenerPublicos()
    {
        $db = Database::getConexion();
        if (!$db) {
            return [];
        }

        $sql = "SELECT e.*, u.nombre AS autor
        FROM eventos e
        JOIN usuarios u ON e.id_usuario = u.id
        WHERE e.es_global = 1
        ORDER BY e.fecha_evento ASC";

        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Consulta las fechas para un usuario que inició sesión:
     * - Si es admin: ve todas las fechas registradas.
     * - Si es alumno: ve las globales (oficiales) + sus propias fechas personales.
     */
    public static function obtenerParaUsuario($idUsuario, $rol)
    {
        $db = Database::getConexion();
        if (!$db) {
            return [];
        }

        if ($rol === 'admin') {
            $sql = "SELECT e.*, u.nombre AS autor
            FROM eventos e
            JOIN usuarios u ON e.id_usuario = u.id
            ORDER BY e.fecha_evento ASC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        }

        // Caso alumno: oficiales + propios
        $sql = "SELECT e.*, u.nombre AS autor
        FROM eventos e
        JOIN usuarios u ON e.id_usuario = u.id
        WHERE e.es_global = 1 OR e.id_usuario = :id_usuario
        ORDER BY e.fecha_evento ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetchAll();
    }

    /**
     * Busca una fecha específica por su ID.
     */
    public static function obtenerPorId($id)
    {
        $db = Database::getConexion();
        if (!$db) {
            return null;
        }

        $stmt = $db->prepare("SELECT * FROM eventos WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Alta de una nueva fecha (Create).
     */
    public static function crear($datos, $idUsuario, $rol)
    {
        $db = Database::getConexion();
        if (!$db) {
            return false;
        }

        // Si es admin puede definir si es global (1) o personal (0); si es alumno siempre es personal (0)
        $esGlobal = 0;
        if ($rol === 'admin') {
            $esGlobal = isset($datos['es_global']) ? (int)$datos['es_global'] : 1;
        }

        $sql = "INSERT INTO eventos (titulo, materia, tipo, fecha_evento, descripcion, id_usuario, es_global)
        VALUES (:titulo, :materia, :tipo, :fecha_evento, :descripcion, :id_usuario, :es_global)";

        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':titulo'       => $datos['titulo'],
            ':materia'      => $datos['materia'],
            ':tipo'         => $datos['tipo'],
            ':fecha_evento' => $datos['fecha_evento'],
            ':descripcion'  => $datos['descripcion'] ?? '',
            ':id_usuario'   => $idUsuario,
            ':es_global'    => $esGlobal
        ]);
    }

    /**
     * Modificación de una fecha (Update) validando permisos.
     */
    public static function actualizar($id, $datos, $idUsuario, $rol)
    {
        $db = Database::getConexion();
        if (!$db) {
            return false;
        }

        if ($rol === 'admin') {
            $esGlobal = isset($datos['es_global']) ? (int)$datos['es_global'] : 1;
            $sql = "UPDATE eventos
            SET titulo = :titulo, materia = :materia, tipo = :tipo,
            fecha_evento = :fecha_evento, descripcion = :descripcion, es_global = :es_global
            WHERE id = :id";
            $params = [
                ':titulo'       => $datos['titulo'],
                ':materia'      => $datos['materia'],
                ':tipo'         => $datos['tipo'],
                ':fecha_evento' => $datos['fecha_evento'],
                ':descripcion'  => $datos['descripcion'] ?? '',
                ':es_global'    => $esGlobal,
                ':id'           => $id
            ];
        } else {
            // El alumno solo puede actualizar sus propias fechas personales
            $sql = "UPDATE eventos
            SET titulo = :titulo, materia = :materia, tipo = :tipo,
            fecha_evento = :fecha_evento, descripcion = :descripcion
            WHERE id = :id AND id_usuario = :id_usuario AND es_global = 0";
            $params = [
                ':titulo'       => $datos['titulo'],
                ':materia'      => $datos['materia'],
                ':tipo'         => $datos['tipo'],
                ':fecha_evento' => $datos['fecha_evento'],
                ':descripcion'  => $datos['descripcion'] ?? '',
                ':id'           => $id,
                ':id_usuario'   => $idUsuario
            ];
        }

        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Baja de una fecha (Delete) validando permisos.
     */
    public static function eliminar($id, $idUsuario, $rol)
    {
        $db = Database::getConexion();
        if (!$db) {
            return false;
        }

        if ($rol === 'admin') {
            $sql = "DELETE FROM eventos WHERE id = :id";
            $params = [':id' => $id];
        } else {
            // El alumno solo puede borrar sus propias fechas personales
            $sql = "DELETE FROM eventos WHERE id = :id AND id_usuario = :id_usuario AND es_global = 0";
            $params = [
                ':id'         => $id,
                ':id_usuario' => $idUsuario
            ];
        }

        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }
}
