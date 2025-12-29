<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderDetail extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'po_detail';
    protected $fillable = ['po_id','pv_id','qunatity','unit_price','total_amount','status','posted_qty','is_posted','posted_at','tax_percentage','tax_amount','inc_tax_total_amount','apply_tax','batch_code','delivery_charge_per_unit','lorry_charge_per_unit','SP_with_profit','profit_value','SP_without_profit'];

    public function purchaseOrder()
    {
        return $this->belongsToMany(PurchaseOrder::class,'po_id','id');
    }

    public function ProductVariant()
    {
        return $this->belongsTo(ProductVariant::class,'pv_id','id');
    }
}
