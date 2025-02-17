<?php
namespace App\Orm;

class BaseModel 
{
    protected string $tableName;
    protected array $dataStructure;
    protected array $fillable;
    protected array $guarded;

    public function beforeSave() {
        //
    }
    
    public function afterSave() {
        //
    }
    
    public function beforeDelete() {
        //
    }
    
    public function afterDelete() {
        //
    }
    
    public function beforeCreate() {
        //
    }
    
    public function afterCreate() {
        //
    }
    
    public function beforeUpdate() {
        //
    }
    
    public function afterUpdate() {
        //
    }
}