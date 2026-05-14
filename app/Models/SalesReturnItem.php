<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\salesOrderDetails;

class SalesReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sr_id',
        'so_detail_id',
        'pv_id',
        'return_qty',
        'rate',
    ];

    public function salesReturn()
    {
        return $this->belongsTo(salesReturn::class,'sr_id','id');
    }

    public function batchAllocations()
    {
        return $this->hasMany(SalesReturnBatchAllocation::class);
    }

    public function salesOrderItem()
    {
        return $this->belongsTo(salesOrderDetails::class,'so_detail_id','id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class,'pv_id','id');
    }
}
