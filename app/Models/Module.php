<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('users', 'user_id');
    }

    public function category()
    {
        return $this->belongsTo('categories', 'module_id');
    }

    public function videos()
    {
        return $this->hasMany('videos', 'module_id');
    }
}
