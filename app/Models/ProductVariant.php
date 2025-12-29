<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'product_variants';

    protected $fillable = ['p_name','product_id','quantity','unit_id','packing_type_id','purchase_price','selling_price','status','stock'];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id','id');
    }

    public function packing_type()
    {
        return $this->belongsTo(PackingType::class,'packing_type_id','id');
    }

    public function PurchaseOrderDetail()
    {
        return $this->hasMany(PurchaseOrderDetail::class,'pv_id','id');
    }

    public function MarginConfig()
    {
        return $this->hasOne(MarginConfig::class,'pv_id','id');
    }

    public function salesOrderDetails()
    {
        return $this->hasOne(salesOrderDetails::class,'pv_id','id');
    }

}
