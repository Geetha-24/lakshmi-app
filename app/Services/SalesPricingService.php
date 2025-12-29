<?php
namespace App\Services;

class SalesPricingService
{
    public static function lineTotal(int $qty, float $price): float
    {
        return $qty * $price;
    }
}
