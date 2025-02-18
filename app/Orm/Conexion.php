<?php
namespace App\Orm;

use Exception;
use MongoDB\Client as MongoClient;
use MongoDB\Database;
use RuntimeException;

class Conexion {

    public $db;
    public string $driver;

    public static function start(): Conexion
    {
        try {
            // $config = require('../../../config/database.php');
            $config = self::findConfigFile('config/database.php');

            $conexion = new self();
            
            return match($config['driver']) {
                'mongodb' => $conexion->connectMongodb($config),
                'mysql' => $conexion->connectMysql($config),
                'postgresql' => $conexion->connectPostgresql($config),
                default => throw new \InvalidArgumentException("Driver no soportado: {$config['driver']}")
            };
            
        } catch (\Exception $e) {
            $statusCode = 500;
            $errorResponse = [
                'message' => 'Error de conexión a la base de datos',
                'status' => $statusCode,
                'errors' => [
                    'database' => [
                        $e->getMessage()
                    ]
                ],
                // 'exception' => get_class($e),
                // 'file' => $e->getFile(),
                // 'line' => $e->getLine(),
                // 'trace' => $e->getTrace()
            ];
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($statusCode);

            echo json_encode($errorResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public static function findConfigFile($file, $maxDepth = 5)
    {
        $dir = __DIR__;
        $depth = 0;
        $dir = dirname($dir, 2);
        while ($depth < $maxDepth) {
            $filePath = $dir . '/' . $file;
            if (file_exists($filePath)) {
                return require $filePath;
            }
            $dir = dirname($dir); // Subir un nivel
            $depth++;
        }
    
        throw new Exception("No se encontró el archivo de configuración '$file'");
    }

    /**
     * Establece conexión con MongoDB
     * @param array $config
     * @return self
     * @throws RuntimeException
     */
    private function connectMongodb(array $config): self
    {
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
            
            // Verificar conexión intentando una operación
            $connection->listDatabases();
            
            $this->driver = $config['driver'];
            $this->db = $connection->selectDatabase($config['db_name']);
            
            return $this;
        } catch (Exception $e) {
            throw new RuntimeException("Error de conexión MongoDB: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Establece conexión con MySQL
     * @param array $config
     * @return self
     * @throws RuntimeException
     */
    private function connectMysql(array $config): self
    {
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
            
            $this->driver = $config['driver'];
            $this->db = $connection;
            
            return $this;
        } catch (Exception $e) {
            throw new RuntimeException("Error de conexión MySQL: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Establece conexión con PostgreSQL
     * @param array $config
     * @return self
     * @throws RuntimeException
     */
    private function connectPostgresql(array $config): self
    {
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
            
            $this->driver = $config['driver'];
            $this->db = $connection;
            
            return $this;
        } catch (Exception $e) {
            throw new RuntimeException("Error de conexión PostgreSQL: " . $e->getMessage(), 0, $e);
        }
    }
}