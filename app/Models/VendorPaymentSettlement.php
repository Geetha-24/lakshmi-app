<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPaymentSettlement extends Model
{
    use HasFactory,SoftDeletes;

    protected $table= 'vendor_payment_settlements';

    protected $fillable = ['vendor_payment_id','po_id','settled_amount'];

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseOrder::class,'po_id','id');
    }

    public function vendorPay()
    {
        return $this->belongsTo(VendorPayment::class,'vendor_payment_id','id');
    }
}
