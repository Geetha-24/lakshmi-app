<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'brands';
    protected $fillable = ['name','status'];

    public function product()
    {
        return $this->hasMany(Products::class,'brand_id');
    }
}
