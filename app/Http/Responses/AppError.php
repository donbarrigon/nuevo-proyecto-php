<?php
namespace App\Http\Responses;

class AppError
{
    // 4xx Client Errors
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const REQUEST_TIMEOUT = 408;
    const CONFLICT = 409;
    const GONE = 410;
    const LENGTH_REQUIRED = 411;
    const PRECONDITION_FAILED = 412;
    const PAYLOAD_TOO_LARGE = 413;
    const URI_TOO_LONG = 414;
    const UNSUPPORTED_MEDIA_TYPE = 415;
    const EXPECTATION_FAILED = 417;
    const UNPROCESSABLE_ENTITY = 422;
    const TOO_MANY_REQUESTS = 429;

    // 5xx Server Errors
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;

    public int $code = 500;
    public string $message;
    public array $errors;

    public static function make(int $code = self::INTERNAL_SERVER_ERROR, string $message = '', array $errors = []): self
    {
        $error = new self();
        $error->code =    $code;
        $error->message = $message;
        $error->errors =  $errors;
        return $error;
    }

    public static function makeAndExit(int $code = self::INTERNAL_SERVER_ERROR, string $message = '', array $errors = []): void
    {
        $errorResponse = [
            'message' => $message,
            'status'  => $code,
            'errors'  => $errors,
        ];
    
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
    
        echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function exitIfThereAreErrors ():void
    {
        if (!empty($this->errors))
        {
            $this->exitJson();
        }
    }

    private function exitJson(): void
    {
        $errorResponse = [
            'message' => $this->message,
            'status'  => $this->code,
            'errors'  => $this->errors,
        ];
    
        http_response_code($this->code);
        header('Content-Type: application/json; charset=utf-8');
    
        echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
        exit;
    }
}