<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'vendors';

    protected $fillable = ['name','contactno','whatsappno','location','mill_name','gst_number','status'];

    public function PurchaseOrder()
    {
        return $this->hasMany(PurchaseOrder::class,'vendor_id');
        
    }


}
