<?php

namespace App\Lib;

use Medoo\Medoo;

/**
 * Database — Librería de conexión centralizada
 * Reutilizable en: index.php, scripts CLI, tests.
 * La BD se configura exclusivamente desde .env (DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS).
 */
class Database
{
    private static ?Medoo $instance = null;
    private static ?string $lastError = null;

    /**
     * Retorna la instancia Medoo. Lanza excepción si falla la conexión.
     */
    public static function connect(): Medoo
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        try {
            $db = new Medoo([
                'database_type' => $_ENV['DB_TYPE']  ?? 'mysql',
                'database_name' => $_ENV['DB_NAME']  ?? '',
                'server'        => $_ENV['DB_HOST']  ?? 'localhost',
                'username'      => $_ENV['DB_USER']  ?? '',
                'password'      => $_ENV['DB_PASS']  ?? '',
                'charset'       => 'utf8mb4',
                'collation'     => 'utf8mb4_unicode_ci',
            ]);

            // Medoo no lanza excepción por sí solo — verificamos con PDO
            $db->pdo->query('SELECT 1');

            self::$instance = $db;
            return $db;

        } catch (\Exception $e) {
            self::$lastError = $e->getMessage();
            throw new \RuntimeException('DB_CONNECTION_FAILED: ' . $e->getMessage());
        }
    }

    /**
     * Retorna el último mensaje de error de conexión.
     */
    public static function lastError(): ?string
    {
        return self::$lastError;
    }

    /**
     * Resetea la instancia (útil en tests).
     */
    public static function reset(): void
    {
        self::$instance = null;
        self::$lastError = null;
    }
}
