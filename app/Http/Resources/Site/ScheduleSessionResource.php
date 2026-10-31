<?php

namespace App\Http\Resources\Site;

use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ClassSession
 */
class ScheduleSessionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $alternative = $this->isFull() ? $this->nextAvailableAlternative() : null;

        return [
            'id' => $this->id,
            'day' => $this->starts_at->toDateString(),
            'time' => $this->starts_at->format('H:i'),
            'endTime' => $this->ends_at->format('H:i'),
            'className' => $this->classType->localized('name'),
            'classSlug' => $this->classType->slug,
            'kind' => $this->classType->kind->value,
            'kindLabel' => $this->classType->kind->getLabel(),
            'level' => $this->classType->level->value,
            'levelLabel' => $this->classType->level->getLabel(),
            'color' => $this->classType->color,
            'isIntro' => $this->classType->is_intro,
            'instructor' => $this->instructor->name,
            'capacity' => $this->effectiveCapacity(),
            'spotsLeft' => $this->spotsLeft(),
            'isFull' => $this->isFull(),
            'bookingUrl' => SiteUrls::booking($this->id),
            'alternative' => $alternative ? [
                'id' => $alternative->id,
                'day' => $alternative->starts_at->toDateString(),
                'dayLabel' => $alternative->starts_at->isoFormat('dddd'),
                'time' => $alternative->starts_at->format('H:i'),
                'className' => $alternative->classType->localized('name'),
                'spotsLeft' => $alternative->spotsLeft(),
                'bookingUrl' => SiteUrls::booking($alternative->id),
            ] : null,
        ];
    }
}
