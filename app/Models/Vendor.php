<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PaymentMode;
class Vendor extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'vendors';

    protected $fillable = ['name','contactno','whatsappno','location','mill_name','gst_number','status','payment_settlement_type'];

    public function PurchaseOrder()
    {
        return $this->hasMany(PurchaseOrder::class,'vendor_id');
        
    }

     public function purchases()
    {
        return $this->hasMany(PurchaseOrder::class,'vendor_id');
    }

     public function payments()
    {
        return $this->hasMany(VendorPayment::class);
    }

     public function ledger()
    {
        return $this->hasMany(VendorLedger::class);
    }

public function paymentSettlements()
{
    return $this->hasManyThrough(
        VendorPaymentSettlement::class,
        VendorPayment::class,
        'vendor_id',          // Foreign key on vendor_payments
        'vendor_payment_id',  // Foreign key on settlements
        'id',
        'id'
    );
}

}
