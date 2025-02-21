<?php
namespace App\Orm;

use MongoDB\Client;
use MongoDB\Database;
use mysqli;
use PgSql\Connection;

class Model 
{

    // /**
    //  * @var Client|Database|mysqli|Connection
    //  */
    // public Client|Database|mysqli|Connection $db;

    /**
     * es el nombre de la tabla o colecion en la base de datos
     * @var string
     */
    public string $name;

    /**
     * @var array<array{type: string, length: int}>
     */
    public array $schema; 

    /**
     * @var array<string>
     */
    public array $fillable;

    /**
     * @var array<string>
     */
    public array $guarded;

    /**
     * @var array<string>
     */
    public array $fields; // se usa para crear los querys
    public array $where;  // se usa para crear los querys
    public array $order;  // se usa para crear los querys
    public int   $limit;  // se usa para crear los querys
    public int   $offset; // se usa para crear los querys
    public array $group;  // se usa para crear los querys

    public mixed $result = []; // almacena los resultados de los querys de momento

    public function __construct(public Client|Database|mysqli|Connection &$db) { }

    // los observers
    public function beforeSave():   ?string { return null; }
    public function afterSave():    ?string { return null; }
    public function beforeDelete(): ?string { return null; }
    public function afterDelete():  ?string { return null; }
    public function beforeCreate(): ?string { return null; }
    public function afterCreate():  ?string { return null; }
    public function beforeUpdate(): ?string { return null; }
    public function afterUpdate():  ?string { return null; }

    public function getSelectFields(array $fields): array
    {
        if (count($fields) === 0) {
            return $this->getAllFieldsNames();
        }

        if (count($this->fields) === 0)
        {
            return $fields;
        }
    
        $result = [];
        foreach ($fields as $field) {
            if ($this->hasField($field)) {
                $result[] = $field;
            }
        }

        return $result;
    }

    public function hasField(string $field): bool
    {
        if (isset($this->schema[$field]))
        {
            return true;
        }
        return false;
    }

    public function getAllFieldsNames(): array
    {
        return array_keys($this->schema);
    }
}