<?php

use App\Orm\Model;

/**
 * Actualiza uno o varios documentos en la base de datos MongoDB según los datos proporcionados.
 *
 * @param Model $model Instancia del modelo que contiene la configuración y conexión a la base de datos.
 * @param array $data Datos a actualizar. Puede ser un solo documento o un array de documentos.
 *
 * @return ?string Retorna un mensaje de error si la actualización falla, o null si tiene éxito.
 *
 * @throws \MongoDB\Driver\Exception\Exception Captura errores relacionados con la base de datos MongoDB.
 * @throws \Exception Captura errores generales durante la validación y actualización.
 *
 * Flujo de la función:
 * - Verifica si el modelo tiene atributos `fillable` o `guarded`.
 * - Si `$data` contiene múltiples documentos, los procesa y usa `bulkWrite` para actualizarlos.
 * - Si `$data` contiene un solo documento, usa `updateOne` para actualizarlo.
 * - Después de la actualización, recupera el documento actualizado y ejecuta `afterUpdate`.
 * 
 *  @mutates $model->result
 */
function orm_update(Model $model, array $data): ?string
{
    try {
        if (empty($model->fillable) && empty($model->guarded)) {
            return "The model must have either fillable or guarded attributes defined.";
        }
        
        $err = $model->beforeUpdate($data);
        if ($err !== null) {
            return $err;
        }

        $collection = $model->db->selectCollection($model->name);

        $processData = function (array $entry) use ($model) {
            $filtered = [];
            foreach ($entry as $key => $value)
            {
                if (!empty($model->fillable) && !in_array($key, $model->fillable, true)) { continue; }

                if (in_array($key, $model->guarded, true)) { continue; }

                if (!isset($model->schema[$key])) { continue; }

                $filtered[$key] = $value;
            }
            return $filtered;
        };

        if (isset($data[0]) && is_array($data[0])) {
            $filteredData = array_map($processData, $data);
            $bulkWrites = [];
            foreach ($filteredData as $entry) {
                if (!isset($entry['_id'])) {
                    return "Missing '_id' field for update.";
                }
                $id = $entry['_id'];
                unset($entry['_id']);
                $bulkWrites[] = [
                    'updateOne' => [
                        ['_id' => $id],
                        ['$set' => $entry]
                    ]
                ];
            }
            $result = $collection->bulkWrite($bulkWrites);
            
            if ($result->getModifiedCount() === 0) {
                return "No records were updated.";
            }
        } else {
            if (!isset($data['_id'])) {
                return "Missing '_id' field for update.";
            }
            $id = $data['_id'];
            unset($data['_id']);
            $filteredData = $processData($data);
            $result = $collection->updateOne(['_id' => $id], ['$set' => $filteredData]);
            
            if ($result->getModifiedCount() === 0) {
                return "No records were updated.";
            }
        }

        //$updatedDocument = $collection->findOne(['_id' => $id]);
        
        $err = $model->afterUpdate($updatedDocument);
        if ($err !== null) {
            return $err;
        }

        $model->result = $updatedDocument;
        return null;

    } catch (\MongoDB\Driver\Exception\Exception $e) {
        return "Failed to update the data: " . $e->getMessage() . " | code: " . $e->getCode();
    } catch (\Exception $e) {
        return "Failed to update the data: Validation error: " . $e->getMessage();
    }
}
