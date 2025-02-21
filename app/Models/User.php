<?php
namespace App\Models;

use App\Orm\Model;
// use App\Orm\Type;

class User extends Model
{
    public string $modelName = 'users';

    public array $fillable = ['name', 'phone', 'email'];

    public array $guarded = ['password'];

    public array $modelStruct = [
        '_id' => [
            'type' => 'int64',
            'unsigned' => true,
            'primary' => true,
            'auto_increment' => true,
            'required' => true,
        ],
        'name' => [
            'type' => 'string',
            'length' => 255,
            'index' => true,
            'required' => true,
        ],
        'phone' => [
            'type' => 'string',
            'length' => 255,
            'unique' => true,
            'required' => true,
        ],
        'email' => [
            'type' => 'string',
            'length' => 255,
            'unique' => true,
            'required' => true,
        ],
        'password' => [
            'type' => 'string',
            'length' => 255,
            'required' => true,
        ],
        'created_at' => [
            'type' => 'timestamp',
            'default' => 'CURRENT_TIMESTAMP',
        ],
        'updated_at' => [
            'type' => 'timestamp',
            'onupdate' => 'CURRENT_TIMESTAMP',
        ],
        'deleted_at' => [
            'type' => 'timestamp',
            'index' => true,
        ],
    ];

    // forma segundaria y con mejor sintaxis de darle la estructura al modelo.
    // public function __construct(public Client|Database|mysqli|Connection $db)
    // {
    //     $this->modelStruct = [
    //         '_id' =>    Type::id(),
    //         'name' =>  Type::string(255, Type::INDEX, Type::REQUIRED),
    //         'phone' => Type::string(255, Type::UNIQUE, Type::REQUIRED),
    //         'email' => Type::string(255, Type::UNIQUE, Type::REQUIRED),
    //         'password' => Type::string(255, Type::REQUIRED),
    //         'created_at' => Type::createdAt(),
    //         'updated_at' => Type::updatedAt(),
    //         'deleted_at' => Type::deletedAt(),
    //     ];
    //     // // otra forma mas rapida de agregar created_at, updated_at y deleted_at
    //     // Type::addTimestamps($this->modelStruct);
    // }
}