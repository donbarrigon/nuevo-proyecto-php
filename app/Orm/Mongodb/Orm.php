<?php
namespace App\Orm\Mongodb;

use MongoDB\Database;
use MongoDB\Exception\Exception;

class Orm
{
    public static function insertOne(Database $db, Model $model, array &$data, array $options = []): array
    {
        if (empty($model->fillable) && empty($model->guarded))
        {
            return [
                'fillable' => 'The model must have either fillable or guarded attributes defined.',
                'guarded' => 'The model must have either fillable or guarded attributes defined.',
            ];
        }

        $err = $model->beforeCreate($data);
        if (empty($err)) {
            return $err;
        }

        //ignoro los campos que no estan en el modelo y los campos protegidos
        $filtered = [];
        foreach ($data as $key => $value)
        {
            if (!in_array($key, $model->fillable, true)) { continue; }

            if (in_array($key, $model->guarded, true)) { continue; }

            if (!isset($model->fields[$key])) { continue; }

            $filtered[$key] = $value;
        }

        // verifico que los campos requeridos esten presentes
        foreach ($model->required as $f) 
        {
            if(!isset($filtered[$f]))
            {
                return [$f => "Field is required."];
            }
        }

        $model->default($filtered);

        $collection = $db->selectCollection($model->name);
        $model->result = $collection->insertOne($filtered, $options);
        
        $err = $model->afterCreate($filtered);
        if ($err !== '') {
            return $err;
        }

        return [];
    }

    public static function insertMany(Database $db, Model $model, array &$data, array $options = []): array
    {
        if (empty($model->fillable) && empty($model->guarded))
        {
            return [
                'fillable' => 'The model must have either fillable or guarded attributes defined.',
                'guarded' => 'The model must have either fillable or guarded attributes defined.',
            ];
        }

        $manyFiltered = [];
        $errors = [];
        foreach ($data as $i => $entry)
        {
            $err = $model->beforeCreate($data);
            if (!empty($err)){
                $errors[$i] = $err;
                continue;
            }

            $filtered = [];
            //ignoro los campos que no estan en el modelo y los campos protegidos
            foreach ($entry as $key => $value)
            {
                if (!in_array($key, $model->fillable, true)) { continue; }

                if (in_array($key, $model->guarded, true)) { continue; }

                if (!isset($model->fields[$key])) { continue; }

                $filtered[$key] = $value;
            }
            // verifico que los campos requeridos esten presentes
            foreach ($model->required as $f) 
            {
                if(!isset($filtered[$f]))
                {
                    $errors[$i][$f] = "Field is required.";
                }
            }

            if (!isset($errors[$i]))
            {
                $model->default($filtered);
                $manyFiltered[] = $filtered;
            }
        }
        
        $collection = $db->selectCollection($model->name);
        $model->result = $collection->insertMany($manyFiltered, $options);

        $err = $model->afterCreate($manyFiltered);
        if (!empty($err)) {
            $errors[] = $err;
        }

        return $errors;
    }

    public static function findById(Database $db, Model $model, string|array $key, array $fields = [], array $options = []): array
    {
        $fields = $model->makeProjection($fields);

        if (empty($model->fields))
        {
            return ['fields' => "There are no valid fields to search in [" . implode(', ', $model->fields) . "]"];
        }

        $collection = $db->selectCollection($model->name);
        if (is_array($key))
        {
            $key = array_map(fn($k) => new MongoDB\BSON\ObjectId($k), $key);
            $model->result = $collection->find(
                ['_id' => ['$in' => $key]],
                ['projection' => $fields],
            );
            
        }else{
            $model->result = $collection->findOne(
                ['_id' => new MongoDB\BSON\ObjectId($key)],
                ['projection' => $fields],
            );
        }
        
        return [];
    }
}