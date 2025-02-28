<?php
namespace App\Models;

use App\Orm\Model;
// use App\Orm\Type;

class User extends Model
{
    protected string $name = 'users';

    protected array $fillable = ['name', 'phone', 'email'];

    protected array $guarded = ['password'];

    protected array $fields = [
        '_id' => [
            'type' => 'id',
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
        ],
        'email' => [
            'type' => 'string',
            'length' => 255,
            'unique' => true,
        ],
        'password' => [
            'type' => 'string',
            'length' => 255,
        ],
        'created_at' => [
            'type' => 'timestamp',
        ],
        'updated_at' => [
            'type' => 'timestamp',
        ],
        'deleted_at' => [
            'type' => 'timestamp',
            'index' => true,
        ],
    ];

    protected function default (array &$data): void
    {
        if (!isset($data['created_at']) || empty($data['created_at']))
        {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
    }

    protected function onUpdate (array &$data): void
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
    }

    // forma segundaria y con mejor sintaxis de darle la estructura al modelo.
    // public function __construct(public Client|Database|mysqli|Connection &$db)
    // {
    //     $this->schema = [
    //         '_id' =>    Type::id(),
    //         'name' =>  Type::string(255, Type::INDEX, Type::REQUIRED),
    //         'phone' => Type::string(255, Type::UNIQUE, Type::REQUIRED),
    //         'email' => Type::string(255, Type::UNIQUE, Type::REQUIRED),
    //         'password' => Type::string(),
    //         'created_at' => Type::createdAt(),
    //         'updated_at' => Type::updatedAt(),
    //         'deleted_at' => Type::deletedAt(),
    //     ];
    //     // // otra forma mas rapida de agregar created_at, updated_at y deleted_at
    //     // Type::addTimestamps($this->modelStruct);
    // }
}