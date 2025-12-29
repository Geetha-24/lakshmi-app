<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseBatch extends Model
{
    use HasFactory;

    protected $table ="purchase_batches";
    protected $fillable = ['po_id','po_detail_id','pv_id','batch_code','purchased_quantity','total_stock_in','sold_qty','purchase_price','selling_price','is_sold','purchase_date'];

}
