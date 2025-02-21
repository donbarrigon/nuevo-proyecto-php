<?php

use PgSql\Connection;

/**
 * Establece conexión con PostgreSQL
 * @return Connection
 * @throws RuntimeException
 */
function orm_connection_start(): Connection
{
    $config = [
        'driver' => getenv('DB_DRIVER') ?: 'mongodb',
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: '27017', // Configuración por defecto para MongoDB
        'db_name' => getenv('DB_NAME') ?: 'app_db',
        'user' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
        'collation' => getenv('DB_COLLATION') ?: 'utf8mb4_general_ci',
    ];
    
    try {
        $connectionString = sprintf(
            "host=%s port=%d dbname=%s user=%s password=%s",
            $config['host'],
            $config['port'],
            $config['db_name'],
            $config['user'],
            $config['password']
        );
        
        // Opciones adicionales de conexión
        $connectionString .= isset($config['ssl']) ? " sslmode={$config['ssl']}" : " sslmode=prefer";
        
        $connection = @pg_connect($connectionString);
        
        if (!$connection) {
            throw new RuntimeException("Error de conexión: " . pg_last_error());
        }
        
        // Configurar el esquema si está especificado
        if (isset($config['schema'])) {
            pg_query($connection, "SET search_path TO {$config['schema']}");
        }
        
        return $connection;
        
    } catch (Exception $e) {
        $message = "Error de conexión con PostgreSQL";
        $statusCode = 500;
        $errorResponse = [
            'message' => $message,
            'status' => $statusCode,
            'errors' => [
                'database' => [
                    $e->getMessage(),
                ]
            ],
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace(),
        ];
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);

        echo json_encode($errorResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}