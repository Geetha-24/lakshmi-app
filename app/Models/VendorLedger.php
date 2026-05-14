<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorLedger extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'vendor_ledgers';
    protected $fillable = ['vendor_id','date','reference_type','reference_id','debit','credit'];

     public function vendor()
    {
        return $this->belongsTo(Vendor::class,'vendor_id');
    }
}
