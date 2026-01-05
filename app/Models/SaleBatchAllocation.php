<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleBatchAllocation extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='sales_batch_allocation';
    protected $fillable = ['so_id','so_detail_id','pb_id','qty','purchase_price','selling_price','profit','status'];

    public function salesOrderDetails()
    {
        return $this->belongsTo(salesOrderDetails::class,'so_detail_id','id');
    }

    public function purchaseBatch()
    {
        return $this->belongsTo(PurchaseBatch::class,'pb_id','id');
    }
    
}
