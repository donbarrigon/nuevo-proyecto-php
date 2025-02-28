<?php
namespace App\Orm;

class Model 
{
    /**
     * es el nombre de la tabla o colecion en la base de datos
     * @var string
     */
    public string $name = '';

    /**
     * @var array<string, array<string, mixed>>
     */
    public array $fields = [];

    /**
     * @var array<string>
     */
    public array $fillable = [];

    /**
     * @var array<string>
     */
    public array $guarded = [];

    /**
     * @var array<string, int>
     */
    protected array $projection = [];

    /**
     * resultado de una busqueda
     * @var array result
     */
    public array $result = [];

    public function __construct() { }

    /**
     * establece los valores por defecto establecidos por cada modelo
     */
    public function default(array &$data): void { }
    public function onUpdate(array &$data): void { }

    public function beforeCreate(array &$data): array { return []; }
    public function afterCreate(array &$data):  array { return []; }

    public function beforeUpdate(array &$data): array { return []; }
    public function afterUpdate(array &$data):  array { return []; }

    public function beforeDelete(array &$data): array { return []; }
    public function afterDelete(array &$data):  array { return []; }
    
    public function beforeRestore(array &$data): array { return []; }
    public function afterRestore(array &$data):  array { return []; }

    public function beforeDestroy(array &$data): array { return []; }
    public function afterDestroy(array &$data):  array { return []; }

    public function hasField(string $field): bool
    {
        if ($this->fields[$field])
        {
            return true;
        }
        return false;
    }

    /**
     *  se asignan los campos que va a traer la consulta
     * @param array<string> $inputFields
     */
    public function setProjection(array $inputFields = []): void
    {
        foreach ($inputFields as $field)
        {
            if (isset($this->fields[$field]))
            {
                $this->projection[$field] = 1;
            }
        }
    }

    /**
     * se optienen los campos que se van a traer de la consulta
     * @return array<string> $projection
     */
    public function getProjection(): array
    {
        if (empty($this->projection))
        {
            return $this->projection;
        }
        $projection = [];
        foreach ($this->fields as $key => $value)
        {
            $projection[$key] = 1;
        }
        return $projection;
    }

    /**
     * retorna un array con los nombres de los campos en la bd
     * @return array<string>
     */
    public function getFields(): array
    {
        $fields = [];
        foreach ($this->fields as $key => $value)
        {
            $fields[] = $key;
        }
        return $fields;
    }

}