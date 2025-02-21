<?php
require_once __DIR__ . "/../../../../vendor/autoload.php";
require_once __DIR__ . "/../../../Orm/Mongodb/orm_connection_start.php";
require_once __DIR__ . "/../../Requests/Functions/validate_request.php";

use App\Http\Requests\RegisterUserRequest;

$request = validate_full_request(new RegisterUserRequest());
$request->exitIfHasErrors();


// echo json_encode($request->errors);
// echo "<hr>";

// $db = orm_connection_start();