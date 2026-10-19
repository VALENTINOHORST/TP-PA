<?php

class Database
{
    private static $host = '127.0.0.1';
    private static $db   = 'calendario_academico';
    private static $user = '2026_grupo4';
    private static $pass = 'gRupp4#1F3k43'; // Por defecto en XAMPP suele estar vacía
    private static $charset = 'utf8mb4';

    private static $conexion = null;
    private static $errorConexion = null;

    public static function getConexion()
    {
        if (self::$conexion === null && self::$errorConexion === null) {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=" . self::$charset;
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$conexion = new PDO($dsn, self::$user, self::$pass, $opciones);
            } catch (PDOException $e) {
                // Guardamos el mensaje amigable requerido por la consigna
                self::$errorConexion = 'En este momento no se pudo conectar con la base de datos. Por favor, intente nuevamente más tarde.';
                // Opcional: registrar para vos internamente en log:
                // error_log($e->getMessage());
            }
        }

        return self::$conexion;
    }

    public static function getErrorConexion()
    {
        return self::$errorConexion;
    }
}
