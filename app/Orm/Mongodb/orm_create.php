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
 * @deprecated Desde la versión 0.3.0, usa orm_insert(Model $model, array $data) en su lugar.
 */
function orm_create(Model $model, array $data): ?string
{
    try {
        
        if (empty($model->fillable) && empty($model->guarded))
        {
            return "The model must have either fillable or guarded attributes defined.";
        }

        $err = $model->beforeCreate($data);
        if ($err !== null) {
            return $err;
        }
        
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
        
        // $collection = $model->db->{$model->name};
        $collection = $model->db->selectCollection($model->name);

        if (isset($data[0]) && is_array($data[0]))
        {
            $filteredData = array_map($processData, $data);
            $result = $collection->insertMany($filteredData);
            
            if (!$result->isAcknowledged()) {
                return 'Failed to insert the data.';
            }

            // Asignar los _id generados a los datos originales
            foreach ($result->getInsertedIds() as $index => $id) {
                $filteredData[$index]['_id'] = $id;
            }
        } else {
            $filteredData = $processData($data);
            $result = $collection->insertOne($filteredData);
            
            if (!$result->isAcknowledged()) {
                return 'Failed to insert the data.';
            }
            
            $filteredData['_id'] = $result->getInsertedId();
        }

        $err = $model->afterCreate($filteredData);
        if ($err !== null) {
            return $err;
        }

        $model->result = $filteredData;
        if ($model->convertObjectIdToString === true)
        {
            if (isset($model->result[0]))
            {
                foreach ($model->result as &$doc) 
                {
                    if (isset($doc['_id']) /*&& $doc['_id'] instanceof MongoDB\BSON\ObjectId*/) {
                        $doc['_id'] = (string) $doc['_id'];
                    }
                }
            }else{
                if (isset($model->result['_id'])) {
                    $model->result['_id'] = (string) $model->result['_id'];
                }
            }
        }
        return null;

    }catch (\MongoDB\Driver\Exception\Exception $e) {
        return "Failed to insert the data: " . $e->getMessage() . " | code: " .$e->getCode();
    } catch (\Exception $e) {
        return "Failed to insert the data: Validation error: " . $e->getMessage();
    }
}