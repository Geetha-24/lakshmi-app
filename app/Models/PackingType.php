<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackingType extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'packing_types';
    protected $fillable = ['name','status'];

    public function ProductVariant()
    {
        return $this->hasMany(ProductVariant::class,'packing_type_id');
    }
}
