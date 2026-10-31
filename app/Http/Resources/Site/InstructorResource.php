<?php

namespace App\Http\Resources\Site;

use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Instructor
 */
class InstructorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'tagline' => $this->localized('tagline'),
            'bio' => $this->localized('bio'),
            'specialties' => $this->localized('specialties'),
            'figure' => $this->figure,
        ];
    }
}
