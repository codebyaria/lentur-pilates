<?php

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Package;
use Illuminate\Support\Str;

/**
 * Pembelian paket. Demo: pembayaran disimulasikan dan langsung lunas.
 */
class PurchasePackage
{
    public function handle(Member $member, Package $package): Membership
    {
        return $member->memberships()->create([
            'package_id' => $package->id,
            'classes_total' => $package->class_count,
            'classes_remaining' => $package->class_count,
            'price_paid' => $package->price,
            'payment_status' => PaymentStatus::Paid,
            'payment_reference' => 'SIM-'.Str::upper(Str::random(10)),
            'purchased_at' => now(),
            'expires_at' => now()->addDays($package->validity_days),
        ]);
    }
}
