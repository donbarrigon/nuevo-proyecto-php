<?php
namespace App\Model;

use App\Orm\Conexion;
use App\Orm\Field;
use App\Orm\Model;

class User extends Model
{
    protected string $modelName = 'users';

    protected array $fillable = ['name', 'phone', 'email'];

    protected array $guarded = ['password'];

    public function __construct(protected Conexion $conexion)
    {
        $this->modelStruct = [
            'id' =>    Field::id(),
            'name' =>  Field::string(),
            'email' => Field::string(),
            'phone' => Field::string(),
        ];
    }
}