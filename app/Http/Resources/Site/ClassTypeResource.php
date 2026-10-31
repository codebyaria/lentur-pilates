<?php

namespace App\Http\Resources\Site;

use App\Models\ClassType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ClassType
 */
class ClassTypeResource extends JsonResource
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
            'kind' => $this->kind->value,
            'kindLabel' => $this->kind->getLabel(),
            'level' => $this->level->value,
            'levelLabel' => $this->level->getLabel(),
            'durationMinutes' => $this->duration_minutes,
            'capacity' => $this->capacity,
            'isIntro' => $this->is_intro,
            'color' => $this->color,
        ];
    }
}
