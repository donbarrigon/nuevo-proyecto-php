<?php

use App\Orm\Model;
use MongoDB\Client;
use MongoDB\Database;

/**
 * Retrieves records from the model's collection based on the given key(s).
 *
 * This function queries the MongoDB collection associated with the model,
 * filtering by `_id`. It also applies field selection based on the model's
 * defined attributes.
 *
 * @param Model $model The model instance, which will be updated with the query result.
 * @param int|string|array $key The primary key(s) to search for.
 * @return string|null Returns an error message on failure, or null on success.
 *
 * @mutates $model->result
 */
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
        $collection = $model->db->selectCollection($model->name);

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