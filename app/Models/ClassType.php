<?php

namespace App\Models;

use App\Enums\ClassKind;
use App\Enums\ClassLevel;
use App\Models\Concerns\HasEnglishCopy;
use Carbon\CarbonImmutable;
use Database\Factories\ClassTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Jenis kelas (mis. "Mat Santai"). Jadwal konkretnya ada di ClassSession.
 *
 * @property int $id
 * @property string $name
 * @property string|null $name_en
 * @property string $slug
 * @property ClassKind $kind
 * @property ClassLevel $level
 * @property string $summary
 * @property string|null $summary_en
 * @property string $description
 * @property string|null $description_en
 * @property int $duration_minutes
 * @property int $capacity
 * @property bool $is_intro
 * @property string $color
 * @property int $sort
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['name', 'name_en', 'slug', 'kind', 'level', 'summary', 'summary_en', 'description', 'description_en', 'duration_minutes', 'capacity', 'is_intro', 'color', 'sort'])]
class ClassType extends Model
{
    /** @use HasFactory<ClassTypeFactory> */
    use HasEnglishCopy, HasFactory;

    /**
     * @return HasMany<ClassSession, $this>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    /**
     * Kelas pertama selalu paling atas.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderByDesc('is_intro')->orderBy('sort');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kind' => ClassKind::class,
            'level' => ClassLevel::class,
            'is_intro' => 'boolean',
        ];
    }
}
