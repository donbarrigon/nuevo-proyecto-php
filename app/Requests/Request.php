<?php
namespace App\Requests;

use App\Responses\AppError;
use Exception;
use JsonException;

class Request
{
    public array  $required = [];
    public array  $methods =  ['GET'];
    public array  $data =     [];
    public array  $errors =   [];
    //public int    $maxSize =  10485760; // 10 MB

    public function validate(): void
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], $this->methods))
        {
            AppError::makeAndExit(AppError::METHOD_NOT_ALLOWED, 'Method not allowed', ['method' => "Method [{$_SERVER['REQUEST_METHOD']}] not allowed"]);
        }

        $input = file_get_contents('php://input');
        $body = [];
        if (!empty($input)) 
        {
            
            // if (!$this->maxSize === 0 || strlen($input) > $this->maxSize)
            // {
            //     AppError::makeAndExit(AppError::BAD_REQUEST, 'Bad request', ['max size' => 'El cuerpo de la solicitud excede el tamaño permitido.']);
            // }

            $body = json_decode($input, true);

            if (!is_array($body))
            {
                AppError::makeAndExit(AppError::BAD_REQUEST, 'Bad request', ['json decode' => 'El cuerpo de la solicitud no es válido.']);
            }
        }

        if (isset($body[0]))
        {
            foreach ($body as $k => $inputData)
            {
                $err = $this->rules($inputData);
                if (!empty($err)) {
                    $this->errors[$k][] = $err;
                }else{
                    $this->data[] = $this->prepareData($inputData);
                }
            }
        }else{
            $inputData = array_merge($_GET, $_POST, $body);
            $err = $this->rules($inputData);
            if (!empty($err)) {
                $this->errors = $err;
            }
            $this->data = $this->prepareData($inputData);
        }
    }

    public function exitIfThereAreErrors(): void
    {
        $appError = AppError::make(422, 'Unprocessable entity', $this->errors);
        $appError->exitIfThereAreErrors();
    }

    public function rules(array &$data): array { return []; }
    public function prepareData(array &$data): array { return []; }
}