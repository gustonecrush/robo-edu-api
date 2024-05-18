<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Uuid;

class Module extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $incrementing = false;
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function videos()
    {
        return $this->hasMany(Video::class, 'module_id');
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
