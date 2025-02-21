<?php
/**
    * Establece conexión con MySQL
    * @return mysqli
    * @throws RuntimeException
    */
function orm_connection_start(): mysqli
{
    $config = [
        'driver' => getenv('DB_DRIVER') ?: 'myqsl',
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: '27017', // Configuración por defecto para MongoDB
        'db_name' => getenv('DB_NAME') ?: 'app_db',
        'user' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
        'collation' => getenv('DB_COLLATION') ?: 'utf8mb4_general_ci',
    ];
    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        $connection = new \mysqli(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['db_name'],
            $config['port']
        );
        
        $charset = $config['charset'] ?? 'utf8mb4';
        $connection->set_charset($charset);
        
        // Configurar el modo SQL si está especificado
        if (isset($config['sql_mode'])) {
            $connection->query("SET sql_mode='{$config['sql_mode']}'");
        }
        
        return $connection;
        
    } catch (Exception $e) {
        $message = "Error de conexión con MySQL";
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