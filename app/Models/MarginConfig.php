<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarginConfig extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['pv_id','profit_amount','status'];

    protected $table = 'margin';

    public function ProductVariant()
    {
        return $this->belongsTo(ProductVariant::class,'pv_id','id');
    }
}
