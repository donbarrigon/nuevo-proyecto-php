<?php
namespace App\Orm;

class Type
{

    public const INDEX =          'index';
    public const UNIQUE =         'unique';
    public const NOT_NULL =       'required';
    public const REQUIRED =       'required';
    public const NULLABLE =       'nullable';
    public const PRIMARY_KEY =    'primary_key';
    public const UNSIGNED =       'unsigned';
    public const SERIAL =         'auto_increment';
    public const AUTO_INCREMENT = 'auto_increment';
    
    public static function id(string ...$constraints): array
    {
        $field = self::defaultID('id');
        return self::processConstraints($field, $constraints);
    }

    public static function bigIncrements(string ...$constraints): array
    {
        $field = self::defaultID('int64');
        return self::processConstraints($field, $constraints);
    }

    public static function Increments(string ...$constraints): array
    {
        $field = self::defaultID('int32');
        return self::processConstraints($field, $constraints);
    }

    public static function smallIncrements(string ...$constraints): array
    {
        $field = self::defaultID('int16');
        return self::processConstraints($field, $constraints);
    }

    public static function tinyIncrements(string ...$constraints): array
    {
        $field = self::defaultID('int8');
        return self::processConstraints($field, $constraints);
    }

    public static function int64(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'int64'], $constraints);
    }

    public static function int32(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'int32'], $constraints);
    }

    public static function int16(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'int16'], $constraints);
    }

    public static function int8(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'int8'], $constraints);
    }

    public static function bigInteger(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'int64'], $constraints);
    }

    public static function integer(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'int32'], $constraints);
    }

    public static function smallInteger(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'int16'], $constraints);
    }

    public static function tinyInteger(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'int8'], $constraints);
    }

    public static function float64(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'float64'], $constraints);
    }

    public static function float32(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'float32'], $constraints);
    }

    public static function time(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'time'], $constraints);
    }

    public static function date(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'date'], $constraints);
    }

    public static function datetime(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'datetime'], $constraints);
    }

    public static function timestamp(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'timestamp'], $constraints);
    }

    public static function longText(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'longtext'], $constraints);
    }

    public static function mediumText(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'mediumtext'], $constraints);
    }

    public static function text(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'text'], $constraints);
    }

    public static function tinyText(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'tinytext'], $constraints);
    }

    public static function longBlob(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'longblob'], $constraints);
    }

    public static function mediumBlob(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'mediumblob'], $constraints);
    }

    public static function blob(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'blob'], $constraints);
    }

    public static function tinyBlob(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'tinyblob'], $constraints);
    }

    public static function bytea(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'bytea'], $constraints);
    }

    public static function json(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'json'], $constraints);
    }

    public static function jsonb(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'jsonb'], $constraints);
    }

    public static function decimal(int $precision = 10, int $scale, string ...$constraints): array
    {
        $field =  [
            'type' => 'string',
            'precision' => $precision,
            'scale' => $scale,
        ];
        return self::processConstraints($field, $constraints);
    }

    public static function string(int $length = 255, string ...$constraints): array
    {
        $field =  [
            'type' => 'string',
            'length' => $length,
        ];
        return self::processConstraints($field, $constraints);
    }

    public static function char(int $length = 255, string ...$constraints): array
    {
        $field =  [
            'type' => 'string',
            'length' => $length,
        ];
        return self::processConstraints($field, $constraints);
    }

    public static function binary(int $length = 255, string ...$constraints): array
    {
        $field =  [
            'type' => 'binary',
            'length' => $length,
        ];
        return self::processConstraints($field, $constraints);
    }

    public static function varBinary(int $length = 255, string ...$constraints): array
    {
        $field =  [
            'type' => 'varbinary',
            'length' => $length,
        ];
        return self::processConstraints($field, $constraints);
    }

    public static function bool(string ...$constraints): array
    {
        return self::processConstraints(['type' => 'bool'], $constraints);
    }

    public static function boolean(string ...$constraints): array
    {
        // $checkConstraint = sprintf("%s IN (TRUE, FALSE)", $name);
        $field =  [
            'type' =>    'boolean',
            'required' => true,
            'default' =>  false,
        ];
        return self::processConstraints($field, $constraints);
    }

    public static function createdAt(string ...$constraints): array
    {
        return [
            'type' => 'timestamp',
            // 'default' => 'CURRENT_TIMESTAMP',
        ];
        return self::processConstraints($field, $constraints);
    }

    public static function updatedAt(string ...$constraints): array
    {
        return [
            'type' => 'timestamp',
            // 'onupdate' => 'CURRENT_TIMESTAMP',
        ];
        return self::processConstraints($field, $constraints);
    }

    public static function deletedAt(string ...$constraints): array
    {
        return [
            'type' => 'timestamp',
            'index' => true,
        ];
        return self::processConstraints($field, $constraints);
    }

    public static function addTimestamps(array &$modelStruct)
    {
        $modelStruct ['created_at'] = self::createdAt();
        $modelStruct ['updated_at'] = self::updatedAt();
        $modelStruct ['deleted_at'] = self::deletedAt();
    }

    private function defaultID(string $type): array
    {
        return [
            'type' => $type,
            'unsigned' => true,
            'primary' => true,
            'auto_increment' => true,
            'required' => true,
        ];
    }

    private function processConstraints (array $field, array $constraints): array
    {
        foreach ($constraints as $constraint)
        {
            switch ($constraint)
            {
                case 'index':
                    $field['index'] = true;
                    break;
                case 'unique':
                    $field['unique'] = true;
                    break;
                case 'not_null' || 'required':
                    $field['required'] = true;
                    break;
                case 'nullable':
                    $field['required'] = false;
                    break;
                case 'primary_key' || 'primary':
                    $field['primary'] = true;
                    break;
                case 'serial' || 'auto_increment':
                    $field['auto_increment'] = true;
                    break;
                case 'unsigned':
                    $field['unsigned'] = true;
                    break;
                default:
                    if (str_starts_with($constraint, 'fk:')) {
                        $field['fk'] = explode(':', $constraint)[1];

                    }else if (str_starts_with($constraint, 'ondelete:')) {
                        $field['ondelete'] = explode(':', $constraint)[1];

                    } else if (str_starts_with($constraint, 'onupdate:')) {
                        $field['onupdate'] = explode(':', $constraint)[1];

                    } else if (str_starts_with($constraint, 'default:')) {
                        $field['default'] = explode(':', $constraint)[1];

                    } else if (str_starts_with($constraint, 'enum:')) {
                        $field['enum'] = explode(':', $constraint)[1];

                    } else if (str_starts_with($constraint, 'check:')) {
                        $field['check'] = explode(':', $constraint)[1];

                    } else if (str_starts_with($constraint, 'comment:')) {
                        $field['comment'] = explode(':', $constraint)[1];

                    } else {
                        $c = explode(':', $constraint);
                        $field[$c[0]] = $c[1];
                    }
                    break;
            }
        }
        return $field;
    }
}

// cosas que me fatan por hacer
// geography
// geometry
// ipAddress
// macAddress
// morphs
// nullableMorphs
// nullableTimestamps
// nullableUlidMorphs
// nullableUuidMorphs
// rememberToken
// set
// ulidMorphs
// uuidMorphs
// ulid
// uuid
// vector
// year