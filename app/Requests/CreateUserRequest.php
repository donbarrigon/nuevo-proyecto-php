<?php
namespace App\Requests;

include 'Functions/validate_fields.php';

use TypeError;

class CreateUserRequest extends Request
{
    public array $required = ['name', ['phone', 'email']];
    
    public array $methods = ['GET', 'POST'];

    /**
     * @param array<string> $data
     * @return array<string>
     */
    public function rules(array &$data): array
    {
        $errors = [];
        if (!empty($data['name'])) 
        { 
            if (is_string($data)){$errors['name']['string'] = 'debe ser una cadena de texto.'; }
            if (mb_strlen($data['name'], 'UTF-8') < 3) { $errors['name']['min'] = 'Debe tener al menos 3 caracteres.'; }
            if (mb_strlen($data['name'], 'UTF-8') > 255) { $errors['name']['max'] = 'No debe tener más de 255 caracteres.'; }
        }else{
            $errors['name'] = 'El campo nombre es requerido.'; 
        }

        if (!empty($data['phone'])) 
        { 
            if (is_string($data)){$errors['phone']['string'] = 'Debe ser una cadena de texto.'; }
            if (mb_strlen($data['phone'], 'UTF-8') < 6) { $errors['phone']['min'] = 'Debe tener al menos 6 caracteres.'; }
            if (mb_strlen($data['phone'], 'UTF-8') > 255) { $errors['phone']['max'] = 'No debe tener más de 255 caracteres.'; }
        }

        if (!empty($data['email'])) 
        { 
            if (is_string($data)){$errors['email']['string'] = 'Debe ser una cadena de texto.'; }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) { $errors['email']['format'] = 'El formato de correo electrónico no es válido.'; }
            if (mb_strlen($data['email'], 'UTF-8') > 255) { $errors['email']['max'] = 'No debe tener más de 255 caracteres.'; }
        }

        if (empty($data['email']) && empty($data['phone'])) 
        {
            $errors['email']['without'] = 'Almenos uno de los campos es requerido.';
            $errors['phone']['without'] = 'Almenos uno de los campos es requerido.';
        }
        return $errors;
    }

    public function prepareData(array &$data): array
    {
        return [
            'name' =>  $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
        ];
    }

}