<?php

return [
    'driver' => getenv('DB_DRIVER') ?: 'mongodb', // Lee el driver desde el .env
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => getenv('DB_PORT') ?: '27017', // Configuración por defecto para MongoDB
    'db_name' => getenv('DB_NAME') ?: 'app_db',
    'user' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    'collation' => getenv('DB_COLLATION') ?: 'utf8mb4_general_ci',
];
