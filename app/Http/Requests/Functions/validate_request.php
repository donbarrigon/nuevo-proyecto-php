<?php

use App\Http\Requests\Request;

function validate_full_request(Request $request): Request
{
    $request->getRequestData();

    $err = $request->prepareForValidation();
    if ( !empty($err) ) {
        $request->errors[] = $err;
    }
    
    $err = validate_rules($request->get, $request->rules());
    if (!empty($err)) {
        $request->errors[] = $err;
    }

    $err = validate_rules($request->post, $request->rules());
    if (!empty($err)) {
        $request->errors[] = $err;
    }

    if (is_array($request->body) && isset($request->body[0]))
    {
        $err[] = validate_rules_collection($request->body, $request->rules());
        if (!empty($err)) {
            $request->errors[] = $err;
        }
    }else{
        $err = validate_rules($request->body, $request->rules());
        if (!empty($err)) {
            $request->errors[] = $err;
        }
    }

    return $request;
}

function validate_get_request(Request $request): Request
{
    $request->getRequestData();

    $err = $request->prepareForValidation();
    if ( !empty($err) ) {
        $request->errors[] = $err;
    }

    $err = validate_rules($request->get, $request->rules());
    if (!empty($err)) { 
        $request->errors[] = $err; 
    }

    return $request;
}

function validate_post_request(Request $request): Request
{
    $request->getRequestData();

    $err = $request->prepareForValidation();
    if ( !empty($err) ) {
        $request->errors[] = $err;
    }

    $err = validate_rules($request->post, $request->rules());
    if (!empty($err)) {
        $request->errors[] = $err;
    }

    return $request;
}

function validate_body_request(Request $request): Request
{
    $request->getRequestData();

    $err = $request->prepareForValidation();
    if ( !empty($err) ) {
        $request->errors[] = $err;
    }

    if (is_array($request->body) && isset($request->body[0]))
    {
        $err[] = validate_rules_collection($request->body, $request->rules());
        if (!empty($err)) {
            $request->errors[] = $err;
        }
    }else{
        $err = validate_rules($request->body, $request->rules());
        if (!empty($err)) {
            $request->errors[] = $err;
        }
    }

    return $request;
}

function validate_rules(array $data, array $rules): array
{
    if(!is_array($data))
    {
        return [-1]['TypeError']["no es un array de datos"];
    }

    $errors = [];
    foreach ($data as $key => $value)
    {
        if ( !isset($rules[$key]) ) { continue; }
        
        $validations = $rules[$key];
        foreach ($validations as $validation)
        {
            $error = $validation($value); 
            if ($error !== null)
            {
                $errors[$key][] = $error;
            }
        }
    }
    return $errors;
}

function validate_rules_collection(array $dataCollection, array $rules): array
{
    $i = 0;
    $errors = [];
    foreach ($dataCollection as $data)
    {
        $err = validate_rules($data, $rules);
        if ( count($err) )
        {
            $errors[$i] = $err;
        }
        $i++;
    }
    return $errors;
}