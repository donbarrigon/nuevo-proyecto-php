<?php
require_once __DIR__ . "/../../../../vendor/autoload.php";
require_once __DIR__ . "/../../../Orm/Mongodb/orm_connection_start.php";
require_once __DIR__ . "/../../../Orm/Mongodb/orm_create.php";
require_once __DIR__ . "/../../Requests/Functions/validate_request.php";

use App\Http\Requests\RegisterUserRequest;
use App\Models\FakeUser;
use App\Models\User;

$request = validate_full_request(new RegisterUserRequest());
//$request->exitIfHasErrors();

$db = orm_connection_start();
$user = new FakeUser($db);

$err = orm_create($user, $request->getAll());

var_dump($err);
echo '<hr>';
var_dump($user);