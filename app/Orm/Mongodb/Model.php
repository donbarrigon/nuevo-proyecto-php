<?php
namespace App\Orm\Mongodb;

class Model 
{
    /**
     * es el nombre de la tabla o colecion en la base de datos
     * @var string
     */
    public string $name = '';

    /**
     * @var array<string>
     */
    public array $fields = [];

    /**
     * @var array<string>
     */
    public array $required = [];

    /**
     * @var array<string>
     */
    public array $fillable = [];

    /**
     * @var array<string>
     */
    public array $guarded = [];

    /**
     * resultado de una busqueda
     * @var mixed result
     */
    public mixed $result = [];

    public function __construct() { }

    /**
     * establece los valores por defecto establecidos por cada modelo
     */
    public function default(array &$data): void { }
    public function onUpdate(array &$data): void { }

    public function beforeCreate(array &$data):  array { return []; }
    public function afterCreate(array &$data):   array { return []; }

    public function beforeUpdate(array &$data):  array { return []; }
    public function afterUpdate(array &$data):   array { return []; }

    public function beforeDelete(array &$data):  array { return []; }
    public function afterDelete(array &$data):   array { return []; }
    
    public function beforeRestore(array &$data): array { return []; }
    public function afterRestore(array &$data):  array { return []; }

    public function beforeDestroy(array &$data): array { return []; }
    public function afterDestroy(array &$data):  array { return []; }

    /**
     *  se asignan los campos que va a traer la consulta
     * @param array<string> $inputFields
     */
    public function makeProjection(array $inputFields = []): array
    {
        if (empty($inputFields))
        {
            return $this->fields;
        }

        $newFields = [];
        foreach ($inputFields as $f)
        {
            if (isset($this->fields[$f]))
            {
                $newFields[$f] = 1;
            }
        }
        return $newFields;
    }

    /**
     * retorna un array con los nombres de los campos en la bd
     * @return array<string>
     */
    public function getFields(): array
    {
        $f = [];
        foreach ($this->fields as $key => $value)
        {
            $f[] = $key;
        }
        return $f;
    }

}