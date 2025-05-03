<?php

// app/Services/InvoiceCodeGenerator.php
namespace App\Services;

use App\Models\InvoiceSequence;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceCodeGenerator
{
    public static function generate(int $companyId): string
    {
        $monthKey = now()->format('Y-m'); // e.g., '2025-05'
        $displayMonth = now()->format('m'); // e.g., '05'

        return DB::transaction(function () use ($companyId, $monthKey, $displayMonth) {
            $sequence = InvoiceSequence::lockForUpdate()->firstOrCreate(
                ['company_id' => $companyId, 'month' => $monthKey],
                ['count' => 0]
            );

            $sequence->increment('count');

            $sequenceNumber = str_pad($sequence->count, 5, '0', STR_PAD_LEFT);

            return "{$displayMonth}-{$sequenceNumber}";
        });
    }
}
