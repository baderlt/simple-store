<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class BladeTranslationKeysTest extends TestCase
{
    #[DataProvider('localeProvider')]
    public function test_static_blade_translation_keys_exist_for_every_locale(string $locale): void
    {
        $missing = [];

        foreach ($this->bladeTranslationKeys() as $key => $locations) {
            if (!$this->translationExists($locale, $key)) {
                $missing[$key] = $locations;
            }
        }

        $details = [];
        foreach ($missing as $key => $locations) {
            $details[] = $key.' ('.implode(', ', $locations).')';
        }

        $this->assertSame([], $missing, "Missing {$locale} Blade translations:\n".implode("\n", $details));
    }

    public static function localeProvider(): array
    {
        return [
            'English' => ['en'],
            'French' => ['fr'],
            'Arabic' => ['ar'],
        ];
    }

    private function bladeTranslationKeys(): array
    {
        $root = dirname(__DIR__, 2);
        $viewsPath = $root.'/resources/views';
        $keys = [];
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));

        foreach ($files as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            preg_match_all(
                '/(?:__|trans|@lang)\(\s*([\'\"])((?:\\\\.|(?!\1).)*)\1/sU',
                $contents,
                $matches,
                PREG_OFFSET_CAPTURE
            );

            foreach ($matches[2] as $index => [$rawKey, $offset]) {
                $quote = $matches[1][$index][0];
                $key = $quote === "'"
                    ? str_replace(["\\'", '\\\\'], ["'", '\\'], $rawKey)
                    : stripcslashes($rawKey);

                if (str_contains($key, '$') || str_contains($key, '{{')) {
                    continue;
                }

                $line = substr_count(substr($contents, 0, $offset), "\n") + 1;
                $relativePath = str_replace($root.'/', '', $file->getPathname());
                $keys[$key][] = $relativePath.':'.$line;
            }
        }

        return $keys;
    }

    private function translationExists(string $locale, string $key): bool
    {
        $langPath = dirname(__DIR__, 2).'/lang';
        $json = json_decode(file_get_contents("{$langPath}/{$locale}.json"), true, flags: JSON_THROW_ON_ERROR);

        if (array_key_exists($key, $json)) {
            return true;
        }

        if (!str_contains($key, '.')) {
            return false;
        }

        [$namespace, $nestedKey] = explode('.', $key, 2);
        $translationFile = "{$langPath}/{$locale}/{$namespace}.php";

        if (!is_file($translationFile)) {
            return false;
        }

        $translation = require $translationFile;
        foreach (explode('.', $nestedKey) as $segment) {
            if (!is_array($translation) || !array_key_exists($segment, $translation)) {
                return false;
            }

            $translation = $translation[$segment];
        }

        return true;
    }
}
