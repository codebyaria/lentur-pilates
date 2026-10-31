<?php

namespace App\Models\Concerns;

/**
 * Kolom `{atribut}_en` dipakai di /en bila terisi; selain itu teks Indonesia.
 */
trait HasEnglishCopy
{
    public function localized(string $attribute, ?string $locale = null): mixed
    {
        $locale ??= app()->getLocale();
        $english = $this->getAttribute("{$attribute}_en");

        if ($locale === 'en' && filled($english)) {
            return $english;
        }

        return $this->getAttribute($attribute);
    }
}
