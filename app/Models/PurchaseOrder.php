<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'purchase_order';
    protected $fillable = ['vendor_id','invoice_number','invoice_date','gross_amount','tax_amount','discount_amount','net_amount','paid_amount','due_amount','payment_status','payment_mode_id','status'];

    public function vendor()
    { 
        return $this->belongsTo(Vendor::class,'vendor_id','id');
    }
    
    public function purchaseOrderDetails()
    {
        return $this->hasMany(PurchaseOrderDetail::class,'po_id','id');
    }

    public function vpsettlement()
    {
        return $this->hasMany(VendorPaymentSettlement::class,'po_id');
    }

    
}
