<?php

if (! function_exists('app_theme')) {
    /**
     * Current user theme: "light" (default) or "dark".
     */
    function app_theme(): string
    {
        return session()->get('theme') === 'dark' ? 'dark' : 'light';
    }
}

if (! function_exists('normalize_theme')) {
    function normalize_theme(?string $theme): string
    {
        return $theme === 'dark' ? 'dark' : 'light';
    }
}
