<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    use HasFactory;
    protected $table="payment_mode_master";

    public function payment()
    {
        return $this->hasOne(Payment::class,'payment_mode_id','id');
    }

}
