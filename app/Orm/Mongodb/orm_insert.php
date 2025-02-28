<?php

use App\Http\Responses\AppError;
use App\Orm\Model;
use MongoDB\Database;

function orm_insert_one(Database $db, Model $model, array &$data, bool $skipValidation  = false): ?AppError
{
    
    return null;
}