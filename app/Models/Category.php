<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Uuid;

class Category extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $incrementing = false;
    public function modules()
    {
        return $this->hasMany(Module::class, 'module_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->id = Uuid::uuid4()->toString();
            } catch (UnsupportedOperationException $e) {
                abort(500, $e->getMessage());
            }
        });
    }
}
