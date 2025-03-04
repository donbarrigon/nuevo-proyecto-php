<?php
namespace App\Orm\Mongodb;

use Exception;
use MongoDB\Client;
use MongoDB\Database;

class Conexion
{
    /**
     * Establece conexión con MongoDB
     * @return Database
     * @throws RuntimeException // and finish endpoint
     */
    public static function start(): Database
    {
        $config = [
            'driver' =>   'mongodb',
            'host' =>     'localhost',
            'port' =>     '27017',
            'db_name' =>  'app_db',
            'user' =>     'root',
            'password' => ''
        ];

        try {
            $options = [
                'serverSelectionTimeoutMS' => 5000,
                'connectTimeoutMS' => 10000,
                'retryWrites' => true
            ];
            
            $connection = new Client(
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

            echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}