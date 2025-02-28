<?php

use App\Orm\Model;
use MongoDB\Exception\Exception;

/**
 * Obtiene todos los registros de una colección en MongoDB.
 * 
 * @param Model $model  Instancia del modelo que contiene la configuración de la consulta.
 * @param array $fields Lista de campos a seleccionar en la consulta.
 * 
 * @return string|null Retorna null si la operación fue exitosa o un mensaje de error si falló.
 */
function orm_get_all(Model $model, array $fields = []): ?string
{
    $model->setFields($fields);
    
    if (empty($model->fields)) {
        return "No hay atributos válidos para buscar en [" . implode(', ', $fields) . "]";
    }

    try {
        $collection = $model->db->selectCollection($model->name);
        $queryOptions = [];
        
        if (!empty($model->fields)) {
            $queryOptions['projection'] = array_fill_keys($model->fields, 1);
        }
        
        if ($model->limit > 0) {
            $queryOptions['limit'] = $model->limit;
        }
        
        if ($model->offset > 0) {
            $queryOptions['skip'] = $model->offset;
        }
        
        $cursor = $collection->find([], $queryOptions);
        $model->result = iterator_to_array($cursor);
        if ($model->convertObjectIdToString === true)
        {
            foreach ($model->result as &$doc) 
            {
                if (isset($doc['_id']) /*&& $doc['_id'] instanceof MongoDB\BSON\ObjectId*/) {
                    $doc['_id'] = (string) $doc['_id'];
                }
            }
        }
        return null;
    } catch (Exception $e) {
        return "Error al obtener registros: " . $e->getMessage();
    }
}

// function orm_get_all_old(Model $model, array $fields = []): ?string
// {
//     $model->setFields($fields);

//     if (empty($model->fields)) {
//         return "No hay atributos válidos para buscar en [" . implode(', ', $model->fields) . "]";
//     }

//     try {
//         $collection = $model->db->selectCollection($model->name);
//         $queryOptions = !empty($model->fields) ? ['projection' => array_fill_keys($model->fields, 1)] : [];
        
//         $cursor = $collection->find([], $queryOptions);
//         $model->result = iterator_to_array($cursor);

//         return null;
//     } catch (Exception $e) {
//         return "Error al obtener registros: " . $e->getMessage();
//     }
// }