<?php

use App\Orm\Model;

/**
 * Inserts data into the model's collection and updates $model->result.
 *
 * This function modifies the $model instance by setting the result attribute.
 *
 * @param Model $model The model instance (modified by reference).
 * @param array $data The data to insert (single record or collection).
 * @return string|null Returns an error message on failure, or null on success.
 * 
 * @mutates $model->result
 */
function orm_create(Model $model, array &$data): ?string
{
    try {
        // $collection = $model->db->{$model->name};
        if (empty($model->fillable) && empty($model->guarded))
        {
            return "The model must have either fillable or guarded attributes defined.";
        }

        $collection = $model->db->selectCollection($model->name);
        
        $processData = function (array $entry) use ($model) 
        {
            $filtered = [];
            foreach ($entry as $key => $value)
            {
                if (!empty($model->fillable) && !in_array($key, $model->fillable, true)) { continue; }

                if (in_array($key, $model->guarded, true)) { continue; }

                if (!isset($model->schema[$key])) { continue; }

                $filtered[$key] = $value;
            }

            foreach ($model->schema as $field => $constraits) 
            {
                if ($field !== '_id' && isset($constraits['required']) && !isset($filtered[$field]))
                {
                    throw new Exception("Field '{$field}' is required.");
                }
            }
            
            return $filtered;
        };

        if (isset($data[0]) && is_array($data[0])) {
            $filteredData = array_map($processData, $data);
            $result = $collection->insertMany($filteredData);
            
            if (!$result->isAcknowledged()) {
                return 'Failed to insert the data.';
            }

            // Asignar los _id generados a los datos originales
            foreach ($result->getInsertedIds() as $index => $id) {
                $data[$index]['_id'] = $id;
            }
        } else {
            $filteredData = $processData($data);
            $result = $collection->insertOne($filteredData);
            
            if (!$result->isAcknowledged()) {
                return 'Failed to insert the data.';
            }
            
            $data['_id'] = $result->getInsertedId();
        }
        $model->result = $data;
        return null;

    }catch (\MongoDB\Driver\Exception\Exception $e) {
        return "Failed to insert the data: " . $e->getMessage() . " | code: " .$e->getCode();
    } catch (\Exception $e) {
        return "Failed to insert the data: Validation error: " . $e->getMessage();
    }
}