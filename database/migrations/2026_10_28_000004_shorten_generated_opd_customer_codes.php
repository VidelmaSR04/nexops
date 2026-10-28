<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $usedCodes = DB::table('customers')->pluck('customer_code')->flip();
        $sequence = $usedCodes->keys()
            ->map(static fn (string $code): int => preg_match('/^OPD(\d+)$/', $code, $matches)
                ? (int) $matches[1]
                : 0)
            ->max() ?? 0;

        DB::table('customers')
            ->where('type', 'opd')
            ->where('customer_code', 'like', 'OPD-%')
            ->orderBy('id')
            ->get(['id'])
            ->each(function (object $customer) use (&$sequence, &$usedCodes): void {
                do {
                    $sequence++;
                    $customerCode = 'OPD'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
                } while ($usedCodes->has($customerCode));

                DB::table('customers')
                    ->where('id', $customer->id)
                    ->update(['customer_code' => $customerCode]);
                $usedCodes->put($customerCode, true);
            });
    }

    public function down(): void
    {
        // Generated codes cannot be safely restored to their original values.
    }
};
