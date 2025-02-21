<?php

use App\Orm\Model;
use MongoDB\Client;
use MongoDB\Database;
use mysqli;
use PgSql\Connection;

function orm_find(Model $model, int|string|array $key): ?string
{
    $model->fields = $model->getSelectFields($model->fields);

    if(count($model->fields) === 0)
    {
        return "No hay atributos válidos para buscar en [" . implode(', ', $model->fields) . "]";
    }

    try {
        // MongoDB utiliza las claves de la proyección como un array
        $selectFields = array_fill_keys($model->fields, 1);
        $collection = $model->db->selectCollection($model->modelName);

        if (is_array($key))
        {
            array_map (fn($k) => is_string($k) && preg_match('/^[0-9a-fA-F]{24}$/', $k) ? new \MongoDB\BSON\ObjectId($k) : $k, $key);
            $filter = ['_id' => ['$in' => $key]];
        } else {
            $key = is_string($key) && preg_match('/^[0-9a-fA-F]{24}$/', $key) ? new \MongoDB\BSON\ObjectId($key) : $key;
            $filter = ['_id' => $key];
        }

        $query = $collection->find($filter, ['projection' => $selectFields]);
        $model->result = iterator_to_array($query);
        
        return null; // Todo bien
    } catch (\Exception $e) {
        return "Error en find Mongodb: " . $e->getMessage();
    }
}