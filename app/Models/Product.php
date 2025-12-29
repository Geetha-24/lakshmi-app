<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'products';

    protected $fillable = ['name','brand_id','category_id','status'];

    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id','id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }

    public function ProductVariant()
    {
        return $this->hasMany(ProductVariant::class,'product_id');
    }
}
