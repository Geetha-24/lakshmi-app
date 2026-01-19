<?php

namespace App\Models;

use App\Services\VendorPaymentService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPayment extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'vendor_payments';
    protected $fillable = ['vendor_id','payment_date','payment_mode','amount','allocated_amount','unallocated_amount','cheque_no'];
    public function vendor()
    {
        return $this->belongsTo(Vendor::class,'vendor_id','id');
    }

     public function paymentModeMaster()
    {
        return $this->belongsTo(PaymentMode::class,'payment_mode','id');
    }

    public function vendorPaySettlement()
    {
        return $this->hasMany(VendorPaymentSettlement::class,'vendor_payment_id','id');
    }

    

    
}
