<?php

use App\Orm\Model;

function orm_find(Model $model, int|string|array $key): ?string
{
    try {
        // Escapar los nombres de los campos
        $selectFields = implode(', ', array_map(function ($field) {
            return '"' . preg_replace('/[^a-zA-Z0-9_]/', '', $field) . '"';
        }, $model->fields));

        if (is_array($key)) {
            // Se usa el operador IN para consultas con múltiples claves
            $placeholders = implode(',', array_map(fn($i) => '$' . ($i + 1), range(0, count($key) - 1)));
            $query = "SELECT $selectFields FROM \"$model->modelName\" WHERE id IN ($placeholders)";
            $stmtName = "find_" . md5($query);
            
            $stmt = @pg_prepare($model->db, $stmtName, $query);
            if (!$stmt) {
                return "Error preparando consulta PostgreSQL: " . pg_last_error($model->db);
            }
            
            $result = pg_execute($model->db, $stmtName, $key);
        } else {
            $query = "SELECT $selectFields FROM \"$model->modelName\" WHERE id = $1";
            $stmtName = "find_single_" . md5($query);
            
            $stmt = @pg_prepare($model->db, $stmtName, $query);
            if (!$stmt) {
                return "Error preparando consulta PostgreSQL: " . pg_last_error($model->db);
            }
            
            $result = pg_execute($model->db, $stmtName, [$key]);
        }

        if (!$result) {
            return "Error ejecutando consulta PostgreSQL: " . pg_last_error($model->db);
        }

        // Recuperar los resultados
        $model->result = pg_fetch_all($result);
        
        return null; // Todo bien
    } catch (\Exception $e) {
        return "Error en findPostgresql: " . $e->getMessage();
    }
}