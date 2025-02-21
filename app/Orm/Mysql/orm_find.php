<?php

use App\Orm\Model;

function orm_find(Model $model, int|string|array $key): ?string
{
    try {
        // Escapa los atributos para asegurarse de que no haya inyecciones
        $selectFields = implode(', ', array_map(function ($field) {
            return "`" . mysqli_real_escape_string($this->conexion->db, $field) . "`"; 
        }, $model->fields));

        if (is_array($key)) 
        {
            $placeholders = implode(',', array_fill(0, count($key), '?'));
            $query = "SELECT $selectFields FROM $model->modelName WHERE id IN ($placeholders)";
        } else {
            $query = "SELECT $selectFields FROM $model->modelName WHERE id = ?";
        }

        // Preparar la consulta
        $stmt = $model->db->prepare($query);
        
        if (!$stmt) {
            return "Error preparando consulta MySQL: " . $model->db->error;
        }

        if (is_array($key)) {
            $types = str_repeat('s', count($key)); // 's' es para string, 'i' es para enteros
            $stmt->bind_param($types, ...$key);
        } else {
            $type = is_int($key) ? 'i' : 's';
            $stmt->bind_param($type, $key);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) {
            return "Error ejecutando consulta MySQL: " . $stmt->error;
        }

        $model->result = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return null; // Todo bien
    } catch (\Exception $e) {
        return "Error en find Mysql: " . $e->getMessage();
    }
}