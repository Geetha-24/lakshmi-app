<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoice extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'purchase_invoice';
     protected $fillable = [
        'vendor_id',
        'bill_no',
        'bill_date',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'status',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class,'vendor_id');
    }

    public function vpsettlement()
    {
        return $this->hasMany(VendorPaymentSettlement::class,'purchase_invoice_id');
    }
}
