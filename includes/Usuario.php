<?php

require_once __DIR__ . '/Database.php';

class Usuario
{
    /**
     * Busca un usuario por su nombre de usuario.
     * Retorna el array asociativo del usuario o null si no existe.
     */
    public static function buscarPorUsuario($nombreUsuario)
    {
        $db = Database::getConexion();
        if (!$db) {
            return null;
        }

        $stmt = $db->prepare('SELECT id, usuario, clave, nombre, rol FROM usuarios WHERE usuario = :usuario LIMIT 1');
        $stmt->execute([':usuario' => $nombreUsuario]);
        $resultado = $stmt->fetch();

        return $resultado ?: null;
    }

    /**
     * Valida las credenciales contra la BD.
     * Retorna el array del usuario si son correctas, o false si no lo son.
     */
    public static function autenticar($nombreUsuario, $contrasena)
    {
        $usuario = self::buscarPorUsuario($nombreUsuario);

        if ($usuario && password_verify($contrasena, $usuario['clave'])) {
            return $usuario;
        }

        return false;
    }
}
