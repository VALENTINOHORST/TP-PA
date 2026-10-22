<?php

require_once __DIR__ . '/../includes/Database.php';

class Evento
{
    
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

        // Caso alumno: todas las públicas + únicamente las privadas que le pertenecen
        $sql = "SELECT e.*, u.nombre AS autor
        FROM eventos e
        JOIN usuarios u ON e.id_usuario = u.id
        WHERE e.es_global = 1 OR e.id_usuario = :id_usuario
        ORDER BY e.fecha_evento ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetchAll();
    }

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

    public static function crear($datos, $idUsuario, $rol)
    {
        $db = Database::getConexion();
        if (!$db) {
            return false;
        }

        $esGlobal = isset($datos['es_global']) ? (int)$datos['es_global'] : 1;

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

    public static function actualizar($id, $datos, $idUsuario, $rol)
    {
        $db = Database::getConexion();
        if (!$db) {
            return false;
        }

        $esGlobal = isset($datos['es_global']) ? (int)$datos['es_global'] : 1;

        if ($rol === 'admin') {
            // El admin puede modificar cualquier evento
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
            // El alumno puede modificar sus propios eventos creados
            $sql = "UPDATE eventos
            SET titulo = :titulo, materia = :materia, tipo = :tipo,
            fecha_evento = :fecha_evento, descripcion = :descripcion, es_global = :es_global
            WHERE id = :id AND id_usuario = :id_usuario";
            $params = [
                ':titulo'       => $datos['titulo'],
                ':materia'      => $datos['materia'],
                ':tipo'         => $datos['tipo'],
                ':fecha_evento' => $datos['fecha_evento'],
                ':descripcion'  => $datos['descripcion'] ?? '',
                ':es_global'    => $esGlobal,
                ':id'           => $id,
                ':id_usuario'   => $idUsuario
            ];
        }

        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

  
    public static function eliminar($id, $idUsuario, $rol)
    {
        $db = Database::getConexion();
        if (!$db) {
            return false;
        }

        if ($rol === 'admin') {
            // El admin puede eliminar cualquier evento
            $sql = "DELETE FROM eventos WHERE id = :id";
            $params = [':id' => $id];
        } else {
            // El alumno puede eliminar sus propios eventos creados
            $sql = "DELETE FROM eventos WHERE id = :id AND id_usuario = :id_usuario";
            $params = [
                ':id'         => $id,
                ':id_usuario' => $idUsuario
            ];
        }

        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }
}
