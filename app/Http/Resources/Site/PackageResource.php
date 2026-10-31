<?php

namespace App\Http\Resources\Site;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Package
 */
class PackageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->localized('name'),
            'summary' => $this->localized('summary'),
            'classCount' => $this->class_count,
            'price' => $this->price,
            'priceLabel' => Package::formatRupiah($this->price),
            'perClassLabel' => Package::formatRupiah($this->pricePerClass()),
            'validityDays' => $this->validity_days,
            'isIntro' => $this->is_intro,
            'isShareable' => $this->is_shareable,
            'bookingUrl' => SiteUrls::booking(query: ['paket' => $this->slug]),
        ];
    }
}
