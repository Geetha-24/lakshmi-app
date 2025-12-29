<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class salesOrderDetails extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'so_detail';

    protected $fillable = ['so_id','pv_id','quantity','fixed_selling_price','sold_price','line_total','line_profit','status'];

    public function SalesOrder()
    {
        return $this->belongsTo(SalesOrder::class,'so_id','id');
    }

    public function ProductVariant()
    {
        return $this->belongsTo(ProductVariant::class,'pv_id','id');
    }

    public function SalesBatch()
    {
        return $this->hasOne(SaleBatchAllocation::class,'so_detail_id','id');
    }
}
