<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'units';
    protected $fillable = ['name','symbol','status'];

    public function ProductVariant()
    {
        return $this->hasMany(ProductVariant::class,'unit_id');
    }
}
