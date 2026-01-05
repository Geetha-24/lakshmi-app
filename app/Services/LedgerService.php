<?php

namespace App\Services;

use App\Models\CustomersLedger;



class LedgerService
{
    public static function debit($customerId, $refType, $refId, $amount)
    {
        self::store($customerId, 'debit', $refType, $refId, $amount);
    }

    public static function credit($customerId, $refType, $refId, $amount)
    {
        self::store($customerId, 'credit', $refType, $refId, $amount);
    }

    private static function store($customerId, $type, $refType, $refId, $amount)
    {
        $lastBalance = CustomersLedger::where('c_id', $customerId)
            ->latest()
            ->value('balance_after') ?? 0;

        $newBalance = $type === 'debit'
            ? $lastBalance + $amount
            : $lastBalance - $amount;

        CustomersLedger::create([
            'c_id'   => $customerId,
            'date'          => now(),
            'type'          => $type,
            'reference_type'=> $refType,
            'reference_id'  => $refId,
            'amount'        => $amount,
            'balance_after' => $newBalance,
        ]);
    }
}
