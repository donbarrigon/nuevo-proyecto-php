<?php
namespace App\Http\Requests;

include 'Functions/validate_fields.php';

use TypeError;

class CreateUserRequest extends Request
{
    public array $required = ['name', ['phone', 'email']];
    
    public array $methods = ['POST'];

    public string $name = '';
    public function name($value): array
    {
        $errors = [];
        try { 
            $this->name = (string)$value;
        } catch (TypeError $e) { 
            $this->name = '';
            $errors['type'] = 'El valor debe ser una cadena de texto.';
        }
        if (($err = validate_min($value, 3)) !==null) { $errors['min'] = $err; }
        
        if (($err = validate_max($value, 255)) !==null) { $errors['max'] = $err; }

        return $errors;
    }

    public string $phone = '';
    public function phone(mixed $value): array
    {
        $errors = [];
        if (empty($value))
        {
            $this->phone = '';
            return $errors;
        }
        try { 
            $this->phone = (string)$value;
        } catch (TypeError $e) { 
            $this->phone = '';
            $errors['type'] = 'El valor debe ser una cadena de texto.';
        }
        if (($err = validate_min($value, 6)) !==null) { $errors['min'] = $err; }

        if (($err = validate_max($value, 255)) !==null) { $errors['max'] = $err; }
        return $errors;
    }

    public string $email = '';
    public function email(mixed $value): array
    {
        $errors = [];
        if (empty($value))
        {
            $this->email = '';
            return $errors;
        }
        try { 
            $this->email = (string)$value;
        } catch (TypeError $e) { 
            $this->email = '';
            $errors['type'] = 'El valor debe ser una cadena de texto.';
        }
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) { $errors['format'] = 'El formato del correo electrónico no es válido.'; }
        
        if (($err = validate_max($value, 255)) !==null) { $errors['max'] = $err; }
        return $errors;
    }

    protected function getArray(): array
    {
        return [
            'name' =>  $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
        ];
    }

}