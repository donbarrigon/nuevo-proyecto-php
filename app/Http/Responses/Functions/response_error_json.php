<?php

function response_error_json(string $message, array $errors, int $statusCode = 500, array $attributes = []): void
{
    $errorResponse = array_merge([
        'message' => $message,
        'status'  => $statusCode,
        'errors'  => $errors
    ], $attributes);

    header('Content-Type: application/json; charset=utf-8');
    http_response_code($statusCode);

    echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
    exit;
}