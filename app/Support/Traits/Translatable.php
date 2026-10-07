<?php

declare(strict_types=1);

namespace App\Support\Traits;

trait Translatable
{
    /**
     * Get a translatable attribute value.
     */
    public function getTranslated(string $attribute, ?string $locale = null): mixed
    {
        $locale = $locale ?: app()->translator()->getLocale();
        
        // 1. Check for locale-specific property (e.g. title_vi)
        $localeSpecificProp = "{$attribute}_{$locale}";
        if (property_exists($this, $localeSpecificProp)) {
            return $this->{$localeSpecificProp};
        }

        // 2. Check for a translations array/JSON if exists
        if (property_exists($this, 'translations') && is_array($this->translations)) {
            if (isset($this->translations[$locale][$attribute])) {
                return $this->translations[$locale][$attribute];
            }
        }

        // Fallback to default property
        return property_exists($this, $attribute) ? $this->{$attribute} : null;
    }

    // NOTE: __get is intentionally NOT defined here.
    // Cycle ORM generates proxy classes that use EntityProxyTrait::__get (without return type),
    // which would be incompatible with a typed __get declaration.
    // Use getTranslated('field') directly instead.
}
