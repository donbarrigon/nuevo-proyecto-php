<?php
namespace App\Models;

use App\Orm\Model;

class FakeUser extends Model
{
    public string $name = 'fake_users';

    public array $fillable = ['name', 'phone', 'email'];

    public array $guarded = ['password'];

    public array $schema = [
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
}