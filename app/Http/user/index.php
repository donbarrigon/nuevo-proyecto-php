<?php
require_once __DIR__ . "/../../../vendor/autoload.php";

use App\Orm\Mongodb\Conexion;
use App\Requests\CreateUserRequest;

// request -----------------------------------------------------------
$request = new CreateUserRequest();
$request->validate();
$request->exitIfThereAreErrors();

$db = Conexion::start();

// middlewares -------------------------------------------------------

// make model --------------------------------------------------------
// $user = new FakeUser($db);

// test find ---------------------------------------------------------
// $err = orm_find($user, [$request->get['id']]);
// if ($err !== null) {
//     response_error_json($err, ['orm find' => $err]);
// }

// test get_all -------------------------------------------------------
// $err = orm_get_all($user);
// if ($err !== null) {
//     response_error_json($err, ['orm get all data' => $err]);
// }

// test create -------------------------------------------------------
// $err = orm_create($user, $request->getAll());
// if($err !== null) {
//     response_error_json($err, ['orm create' => $err]);
// }

// respose -----------------------------------------------------------
http_response_code(200);
header('Content-Type: application/json; charset=utf-8');

echo json_encode($request->data, JSON_UNESCAPED_UNICODE);
exit;