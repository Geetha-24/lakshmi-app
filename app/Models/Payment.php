<?php

namespace App\Models;

use Database\Seeders\PaymentModeMaster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payment';

    protected $fillable = [
        'c_id',
        'so_id',
        'payment_date',
        'amount',
        'payment_mode_id',
        'reference_no',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class,'so_id','id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'c_id','id');
    }

    public function paymentModeMaster()
    {
        return $this->belongsTo(PaymentMode::class,'payment_mode_id','id');
    }
}
