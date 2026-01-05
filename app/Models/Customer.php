<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'customers';
    protected $fillable = ['name','contactno','whatsappno','location','mill_name','gst_number','status','type'];

    public function SalesOrder()
    {
        return $this->hasOne(SalesOrder::class,'customer_id','id');
    }

    public function Payment()
    {
        return $this->hasMany(Payment::class,'c_id','id');

    }

    public function customerLedger()
    {
        return $this->hasOne(CustomersLedger::class,'c_id','id');
    }


}
