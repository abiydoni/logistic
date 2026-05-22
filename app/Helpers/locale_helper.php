<?php

if (! function_exists('normalize_locale')) {
    function normalize_locale(?string $locale): string
    {
        return in_array($locale, ['en', 'id'], true) ? $locale : 'id';
    }
}

if (! function_exists('app_locale')) {
    /**
     * Current app language: "id" (default) or "en".
     */
    function app_locale(): string
    {
        $lang = session()->get('lang');

        if (! in_array($lang, ['en', 'id'], true)) {
            $lang = service('request')->getLocale();
        }

        return normalize_locale($lang);
    }
}

/**
 * Build URL for language switch (login / guest), keeping current path and query params.
 */
function lang_switch_url(string $locale): string
{
    $locale = normalize_locale($locale);
    $params = service('request')->getGet();
    $params['lang'] = $locale;

    return current_url() . '?' . http_build_query($params);
}
