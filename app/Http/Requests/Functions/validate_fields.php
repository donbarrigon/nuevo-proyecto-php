<?php

function validate_max(mixed $value, int|float $max): ?string
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

function validate_min(mixed $value, int|float $min): ?string
{
    if (is_string($value)) {
        if (mb_strlen($value) < $min) {
            return "El texto debe tener al menos $min caracteres.";
        }
    } else if (is_numeric($value)) {
        if ($value < $min) {
            return "El valor debe ser mayor o igual a $min.";
        }
    } else if (is_array($value)) {
        if (count($value) < $min) {
            return "La lista debe tener al menos $min elementos.";
        }
    } else {
        return "No se puede validar el tipo de dato proporcionado.";
    }
    
    return null;
}