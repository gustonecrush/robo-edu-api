<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Uuid;

class Video extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $incrementing = false;
    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
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
