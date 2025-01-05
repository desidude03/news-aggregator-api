<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name']; // Allow mass assignment for 'name'

    public function articles()
    {
        return $this->hasMany(Articles::class);
    }
}
