<?php
namespace App\Http\Requests;
require_once __DIR__ . '/../Responses/Functions/response_error_json.php';

class Request
{
    public array $post;
    public array $get;
    public array $files;
    public array $body;
    public array $errors = [];
    public array $rules = [];

    public function getRequestData(): void 
    {
        $this->post = &$_POST;
        $this->get = &$_GET;
        $this->files = &$_FILES;

        if (!isset($_SERVER['CONTENT-TYPE']) || strpos($_SERVER['CONTENT-TYPE'], 'application/json') === false)
        {
            $this->body = [];
            return;
        }

        $input = file_get_contents('php://input');
        
        if (empty($input))
        {
            $this->body = [];
            return;
        }

        try {
            $this->body = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->body = [];
            $this->errors['raw body'] = $e->getMessage();
        }
    }

    public function prepareForValidation(): array { return []; }

    public function rules(): array
    {
        return [];
    }

    public function exitIfHasErrors(): void
    {
        if (!empty($this->errors))
        {
            response_error_json('bad request', $this->errors, 400);
        }
    }
}