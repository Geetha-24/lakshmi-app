<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnBatchAllocation extends Model
{
    use HasFactory;

     protected $table = 'sales_return_batch_allocation';

    protected $fillable = [
        'sr_id',
        'sales_return_item_id',
        'pb_id',
        'qty_in',
    ];
}
