<?php

namespace App\Models;

use App\Models\Concerns\HasEnglishCopy;
use Carbon\CarbonImmutable;
use Database\Factories\InstructorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $tagline
 * @property string|null $tagline_en
 * @property string $bio
 * @property string|null $bio_en
 * @property list<string> $specialties
 * @property list<string>|null $specialties_en
 * @property int $figure
 * @property bool $is_active
 * @property int $sort
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['name', 'slug', 'tagline', 'tagline_en', 'bio', 'bio_en', 'specialties', 'specialties_en', 'figure', 'is_active', 'sort'])]
class Instructor extends Model
{
    /** @use HasFactory<InstructorFactory> */
    use HasEnglishCopy, HasFactory;

    /**
     * @return HasMany<ClassSession, $this>
     */
    public function classSessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'specialties' => 'array',
            'specialties_en' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
