<?php
namespace App\Http\Requests;

require_once __DIR__ . "/Functions/validate_fields.php";

use App\Http\Requests\Request;

class RegisterUserRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => [
                fn($data) => validate_required($data),
                fn($data) => validate_string($data),
                fn($data) => validate_max($data, 255),
            ],
            'phone' => [
                fn($data) => validate_required($data),
                fn($data) => validate_string($data),
                fn($data) => validate_max($data, 255),
            ],
            'email' => [
                fn($data) => validate_required($data),
                fn($data) => validate_email($data),
                fn($data) => validate_max($data, 255),
            ],
            'password' => [
                fn($data) => validate_required($data),
                fn($data) => validate_max($data, 20),
                fn($data) => validate_min($data, 8),
            ],
        ];
    }
}