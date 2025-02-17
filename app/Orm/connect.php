<?php

namespace App\Orm;

function connect(): \mysqli|\PDO|\MongoDB\Database
{
    $config = require('config/database.php');

    if ($config['driver'] === 'mongodb') {
        return connectMongodb($config);
    }

    if ($config['driver'] === 'mysql') {
        return connectMysql($config);
    }

    if ($config['driver'] === 'postgresql') {
        return connectPostgresql($config);
    }

    throw new \Exception("Unsupported driver: {$config['driver']}");
}

function connectMongodb(array $config): \MongoDB\Database
{
    // Crear la conexión MongoDB usando la extensión MongoDB de PHP
    try {
        $connection = new \MongoDB\Client("mongodb://{$config['host']}:{$config['port']}");
        return $connection->selectDatabase($config['db_name']);
    } catch (\Exception $e) {
        throw new \Exception("Connection failed: " . $e->getMessage());
    }
}

function connectMysql(array $config): \mysqli
{
    // Crear la conexión mysqli
    $connection = new \mysqli(
        $config['host'],
        $config['user'],
        $config['password'],
        $config['db_name'],
        $config['port']
    );

    // Verificar la conexión
    if ($connection->connect_error) {
        throw new \Exception("Connection failed: " . $connection->connect_error);
    }

    // Establecer el charset para la conexión
    $connection->set_charset($config['charset']);

    return $connection;
}

function connectPostgresql(array $config)
{
    // Crear la conexión PostgreSQL usando pg_connect
    $connectionString = "host={$config['host']} port={$config['port']} dbname={$config['db_name']} user={$config['user']} password={$config['password']}";
    
    $connection = pg_connect($connectionString);
    
    // Verificar si la conexión es exitosa
    if (!$connection) {
        throw new \Exception("Connection failed: " . pg_last_error());
    }

    return $connection;
}

// function connectPostgresql(array $config): \PDO
// {
//     // Crear la conexión PostgreSQL usando PDO
//     try {
//         $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['db_name']}";
//         $connection = new \PDO($dsn, $config['user'], $config['password']);
        
//         // Configuración del modo de error de PDO
//         $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
//         return $connection;
//     } catch (\PDOException $e) {
//         throw new \Exception("Connection failed: " . $e->getMessage());
//     }
// }