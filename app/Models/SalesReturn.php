<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'so_id',
        'customer_id',
        'return_no',
        'return_date',
        'total_amount',
        'refund_amount',
        'refund_type',
        'status',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(SalesReturnItem::class,'sr_id','id');
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class,'so_id','id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id','id');
    }
}
