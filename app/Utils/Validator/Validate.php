<?php
namespace App\Utils\Validator;

class Validate
{
    public static function email(string $value): ?string
    {
        if (empty($value)) {
            return "El formato del correo electrónico no es válido.";
        }
        
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return "El formato del correo electrónico no es válido.";
        }
        
        return null;
    }

    public static function min(mixed $value, int|float $min): ?string
    {
        if (is_string($value)) {
            if (mb_strlen($value) < $min) {
                return "El texto debe tener al menos $min caracteres.";
            }
        } elseif (is_numeric($value)) {
            if ($value < $min) {
                return "El valor debe ser mayor o igual a $min.";
            }
        } elseif (is_array($value)) {
            if (count($value) < $min) {
                return "La lista debe tener al menos $min elementos.";
            }
        } else {
            return "No se puede validar el tipo de dato proporcionado.";
        }
        
        return null;
    }

    public static function max(mixed $value, int|float $max): ?string
    {
        if (is_string($value)) {
            if (mb_strlen($value) > $max) {
                return "El texto no debe exceder los $max caracteres.";
            }
        } elseif (is_numeric($value)) {
            if ($value > $max) {
                return "El valor debe ser menor o igual a $max.";
            }
        } elseif (is_array($value)) {
            if (count($value) > $max) {
                return "La lista no debe exceder los $max elementos.";
            }
        } else {
            return "No se puede validar el tipo de dato proporcionado.";
        }
        
        return null;
    }

    public static function required(mixed $value): ?string
    {
        if (is_null($value)) {
            return "Este campo es obligatorio.";
        }
        
        if (is_string($value) && trim($value) === '') {
            return "Este campo es obligatorio.";
        }
        
        if (is_array($value) && count($value) === 0) {
            return "Este campo es obligatorio.";
        }
        
        return null;
    }

    public static function string(mixed $value): ?string
    {
        if (!is_string($value) && !is_null($value)) {
            return "El valor debe ser una cadena de texto.";
        }
        
        return null;
    }

    public static function integer(mixed $value): ?string
    {
        if (is_null($value) || $value === '') {
            return "El valor debe ser un número entero.";
        }
        
        if (!is_numeric($value) || intval($value) != $value) {
            return "El valor debe ser un número entero.";
        }
        
        return null;
    }

    public static function float(mixed $value): ?string
    {
        if (is_null($value) || $value === '') {
            return "El valor debe ser un número decimal.";
        }
        
        if (!is_numeric($value)) {
            return "El valor debe ser un número decimal.";
        }
        
        return null;
    }

    public static function date(mixed $value): ?string
    {
        if (is_null($value) || $value === '') {
            return null;
        }
        
        $date = date_parse($value);
        if ($date['error_count'] > 0 || $date['warning_count'] > 0) {
            return "El formato de fecha no es válido.";
        }
        
        return null;
    }
}