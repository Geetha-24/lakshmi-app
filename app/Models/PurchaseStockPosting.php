<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseStockPosting extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'purchase_stock_posting';

    protected $fillable = ['po_detail_id','batch_code','quantity','posted_qty','remaining_qty','posting_date','status'];
}
