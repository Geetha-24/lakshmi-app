<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SalesOrder extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'sales_orders';

    protected $fillable = [
        'so_number','customer_id','order_date','status',
        'subtotal','tax_amount','total_amount','total_profit'
    ];

    public function Customer()
    {
        return $this->belongsTo(Customer::class,'customer_id','id');
    }

    public function salesOrderDetails()
    {
        return $this->hasMany(salesOrderDetails::class,'so_id','id');
    }

    public static function generateSoNumber(): string
    {
        $today = Carbon::today();

        // Count today's sales
        $todaySalesCount = SalesOrder::withTrashed()->whereDate('created_at', $today)->count();

        // Next number
        $nextNumber = $todaySalesCount + 1;

        // Format date ddmmyy
        $datePart = $today->format('dmy'); // e.g., 22122025

        return "SLT-{$nextNumber}-{$datePart}";
    }
}
