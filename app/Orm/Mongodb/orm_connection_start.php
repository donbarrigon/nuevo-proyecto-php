<?php
use MongoDB\Client as MongoClient;
use MongoDB\Database;

/**
 * Establece conexión con MongoDB
 * @return Database
 * @throws RuntimeException // and finish endpoint
 */
function orm_connection_start(): Database
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
        $options = [
            'serverSelectionTimeoutMS' => 5000,
            'connectTimeoutMS' => 10000,
            'retryWrites' => true
        ];
        
        $connection = new MongoClient(
            "mongodb://{$config['host']}:{$config['port']}", 
            $options
        );

        return $connection->selectDatabase($config['db_name']);
        
    } catch (Exception $e) {
        $message = "Error de conexión con MongoDB: ";
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