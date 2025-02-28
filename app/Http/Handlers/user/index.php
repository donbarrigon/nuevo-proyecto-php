<?php
require_once __DIR__ . "/../../../../vendor/autoload.php";
// require_once __DIR__ . "/../../../Orm/Mongodb/orm_connection_start.php";
// require_once __DIR__ . "/../../../Orm/Mongodb/orm_create.php";
// require_once __DIR__ . "/../../../Orm/Mongodb/orm_find.php";
// require_once __DIR__ . "/../../../Orm/Mongodb/orm_get_all.php";

use App\Http\Requests;
use App\Http\Requests\CreateUserRequest;
use App\Models\FakeUser;

// methods allow -----------------------------------------------------
// if ($_SERVER['REQUEST_METHOD'] !== 'GET')
// {
//     response_error_json(
//         "Only GET requests are allowed.",
//         ['method' => "Method [{$_SERVER['REQUEST_METHOD']}] not allowed" ],
//         405
//     );
//     exit;
// }

// request -----------------------------------------------------------
$request = CreateUserRequest::validate();
$request->exitIfThereAreErrors();

// $db = orm_connection_start();

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