<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Number;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $summary
 * @property int $class_count
 * @property int $price
 * @property int $validity_days
 * @property bool $is_intro
 * @property bool $is_shareable
 * @property bool $is_active
 * @property int $sort
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['name', 'slug', 'summary', 'class_count', 'price', 'validity_days', 'is_intro', 'is_shareable', 'is_active', 'sort'])]
class Package extends Model
{
    /** @use HasFactory<PackageFactory> */
    use HasFactory;

    /**
     * @return HasMany<Membership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function pricePerClass(): int
    {
        return intdiv($this->price, max(1, $this->class_count));
    }

    public static function formatRupiah(int $amount): string
    {
        return 'Rp '.Number::format($amount, locale: 'id');
    }

    /**
     * Paket kelas pertama paling atas.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function available(Builder $query): void
    {
        $query->where('is_active', true)->orderByDesc('is_intro')->orderBy('sort');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_intro' => 'boolean',
            'is_shareable' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
