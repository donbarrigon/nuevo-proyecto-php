<?php
namespace App\Http\Requests;

use App\Http\Responses\AppError;
use Exception;
use JsonException;

class Request
{
    public array  $required = [];
    public array  $methods =  ['GET'];
    public array  $data =     [];
    public array  $errors =   [];
    public int    $maxSize =  10485760; // 10 MB

    /**
     * @return array<string>
     */
    public function prepareForValidation(array &$body): array { return []; }

    public static function validate(): static
    {
        
        $request = new static();

        if (!in_array($_SERVER['REQUEST_METHOD'], $request->methods))
        {
            AppError::makeAndExit(AppError::METHOD_NOT_ALLOWED, 'Method not allowed', ['method' => "Method [{$_SERVER['REQUEST_METHOD']}] not allowed"]);
        }

        $input = file_get_contents('php://input');
        $body = [];
        if (!empty($input)) 
        {
            
            if (strlen($input) > $request->maxSize)
            {
                AppError::makeAndExit(AppError::BAD_REQUEST, 'Bad request', ['size' => 'El cuerpo de la solicitud excede el tamaño permitido.']);
            }

            $body = json_decode($input, true);

            if (!is_array($body))
            {
                AppError::makeAndExit(AppError::BAD_REQUEST, 'Bad request', ['json decode' => 'El cuerpo de la solicitud no es válido.']);
            }
        }

        $err = $request->prepareForValidation($body);
        if ( !empty($err) )
        {
            AppError::makeAndExit(AppError::UNPROCESSABLE_ENTITY, 'Unprocessable entity', $err);
        }

        if (isset($body[0]))
        {
            foreach ($body as $k => $data)
            {
                $err = $request->validateFields($data);
                if (!empty($err)) {
                    $request->errors[$k][] = $err;
                }
                $request->data[] = $request->getArray();
            }
            return $request;   
        }    
        
        $data = array_merge($_GET, $_POST, $body);
        $err = $request->validateFields($data);
        if (!empty($err)) {
            $request->errors = $err;
        }
        $request->data = $request->getArray();
        return $request;
    }

    protected function validateFields(array &$data): array
    {
        
        $errors = [];
        foreach ($data as $key => $value)
        {
            if (method_exists($this, $key))
            {
                $err = $this->$key($value);
                if ( !empty($err) ) {
                    $errors[$key] = $err;
                }
            }
        }

        foreach ($this->required as $field)
        {
            if (is_array($field))
            {
                $without = false;
                foreach ($field as $f)
                {
                    if (!empty($this->$f))
                    {
                        $without = true;
                        break;
                    }
                }
                if ($without === false)
                {
                    foreach ($field as $f)
                    {
                        $errors[$f] = ['without' => "Almenos uno de los campos es requerido"];
                    }
                }
            }else{
                if (empty($this->$field))
                {
                    $errors[$field] = ['required' => "El campo es requerido"];
                }
            }
        }
        return $errors;
    }

    public function exitIfThereAreErrors(): void
    {
        $appError = AppError::make(422, 'Unprocessable entity', $this->errors);
        $appError->exitIfThereAreErrors();
    }

    protected function getArray(): array { return []; }
}