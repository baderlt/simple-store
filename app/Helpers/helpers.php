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

if (! function_exists('bidi_text_direction')) {
    /**
     * Detect the first strong writing direction in a text fragment.
     */
    function bidi_text_direction(string $text, string $fallback = 'ltr'): string
    {
        if (! preg_match('/[\p{Arabic}\p{Hebrew}\p{Latin}]/u', $text, $match)) {
            return preg_match('/\d/u', $text) ? 'ltr' : $fallback;
        }

        return preg_match('/[\p{Arabic}\p{Hebrew}]/u', $match[0]) ? 'rtl' : 'ltr';
    }
}

if (! function_exists('bidi_text')) {
    /**
     * Safely render mixed Arabic/Latin labels while preserving segment order.
     *
     * Product names commonly use visual separators such as " - " and " / ".
     * Each semantic segment is direction-isolated, while the wrapper follows
     * the first strong character of the complete value.
     */
    function bidi_text(?string $text): string
    {
        $value = trim((string) $text);

        if ($value === '') {
            return '';
        }

        $baseDirection = bidi_text_direction($value);
        $parts = preg_split(
            '/(\s+(?:[-–—\/|•])\s+|\s*[|•]\s*)/u',
            $value,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        ) ?: [$value];

        $html = '';

        foreach ($parts as $part) {
            $escaped = htmlspecialchars($part, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $isSeparator = preg_match('/^(?:\s+(?:[-–—\/|•])\s+|\s*[|•]\s*)$/u', $part);
            $direction = $isSeparator ? 'ltr' : bidi_text_direction($part, $baseDirection);
            $class = $isSeparator ? 'bidi-text-separator' : 'bidi-text-segment';

            $html .= sprintf(
                '<bdi class="%s" dir="%s" style="unicode-bidi:isolate;">%s</bdi>',
                $class,
                $direction,
                $escaped
            );
        }

        return sprintf(
            '<span class="bidi-text" dir="%s" style="unicode-bidi:isolate; text-align:start;">%s</span>',
            $baseDirection,
            $html
        );
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
