<?php

use App\Services\StoreSettingsService;

if (!function_exists('settings')) {
    function settings($key, $default = null) {
        return app(StoreSettingsService::class)->get($key, $default);
    }
}

if (! function_exists('format_price')) {
    function format_price($amount): string
    {
        return rtrim(rtrim(number_format((float) $amount, 2, '.', ''), '0'), '.');
    }
}

if (! function_exists('working_hours_parts')) {
    /**
     * Convert the configurable working-hours sentence into displayable rows.
     *
     * Supported separators: pipe, comma, Arabic comma, semicolon, backslash
     * and line breaks. Each row may use the format "days: hours".
     */
    function working_hours_parts(?string $workingHours = null): array
    {
        $value = trim((string) ($workingHours ?? settings('working_hours', '')));

        if ($value === '') {
            return [];
        }

        $value = str_replace('\\', '|', $value);
        $periods = preg_split('/\s*(?:\||,|،|;|\R)\s*/u', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $structuredPeriods = [];

        foreach ($periods as $period) {
            [$days, $hours] = array_pad(array_map('trim', explode(':', $period, 2)), 2, '');

            if ($days !== '' || $hours !== '') {
                $structuredPeriods[] = [
                    'days' => $days,
                    'hours' => $hours,
                ];
            }
        }

        return $structuredPeriods;
    }
}
