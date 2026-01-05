<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomersLedger extends Model
{
    use HasFactory;
    protected $table = 'customer_ledgers';

    protected $fillable = ['c_id','date','type','reference_type','reference_id','amount','balance_after'];

    public function customer()
    {
        return $this->belongsTo(Customer::class,'c_id','id');
    }


}
