<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class TranslateStaticHtml
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (App::getLocale() !== 'ar' || ! $this->isHtmlResponse($response)) {
            return $response;
        }

        $translations = $this->translations();
        if ($translations === []) {
            return $response;
        }

        uksort($translations, fn (string $a, string $b): int => mb_strlen($b) <=> mb_strlen($a));
        $response->setContent($this->translateHtml((string) $response->getContent(), $translations));

        return $response;
    }

    private function translations(): array
    {
        $translations = $this->pairedLocaleTranslations();
        $static = trans('static');

        if (is_array($static)) {
            $translations = array_merge($translations, $static);
        }

        return array_filter(
            $translations,
            fn ($translation, $source): bool => is_string($source)
                && is_string($translation)
                && $source !== ''
                && $source !== $translation,
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function pairedLocaleTranslations(): array
    {
        $translations = [];

        foreach (glob(lang_path('fr/*.php')) ?: [] as $frenchFile) {
            $arabicFile = lang_path('ar/'.basename($frenchFile));
            if (! is_file($arabicFile)) {
                continue;
            }

            $french = require $frenchFile;
            $arabic = require $arabicFile;

            if (is_array($french) && is_array($arabic)) {
                $this->pairTranslations($french, $arabic, $translations);
            }
        }

        return $translations;
    }

    private function pairTranslations(array $french, array $arabic, array &$translations): void
    {
        foreach ($french as $key => $source) {
            if (! array_key_exists($key, $arabic)) {
                continue;
            }

            $translation = $arabic[$key];
            if (is_array($source) && is_array($translation)) {
                $this->pairTranslations($source, $translation, $translations);
            } elseif (is_string($source) && is_string($translation)) {
                $translations[$source] = $translation;
            }
        }
    }

    private function translateHtml(string $html, array $translations): string
    {
        $parts = preg_split(
            '/(<(?:script|style)\b[^>]*>.*?<\/(?:script|style)>|<[^>]+>)/is',
            $html,
            -1,
            PREG_SPLIT_DELIM_CAPTURE
        );

        if ($parts === false) {
            return $html;
        }

        foreach ($parts as &$part) {
            if ($part === '' || preg_match('/^<(?:script|style)\b/is', $part)) {
                continue;
            }

            if (str_starts_with($part, '<')) {
                $part = $this->translateAttributes($part, $translations);
            } else {
                $part = strtr($part, $translations);
            }
        }

        return implode('', $parts);
    }

    private function translateAttributes(string $tag, array $translations): string
    {
        return preg_replace_callback(
            '/\b(placeholder|title|aria-label)=("|\')(.*?)\2/is',
            fn (array $matches): string => $matches[1].'='.$matches[2].strtr($matches[3], $translations).$matches[2],
            $tag
        ) ?? $tag;
    }

    private function isHtmlResponse(Response $response): bool
    {
        if (! $response->isSuccessful() || ! method_exists($response, 'getContent')) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type');

        if (str_contains($contentType, 'text/html')) {
            return true;
        }

        if ($contentType !== '') {
            return false;
        }

        return str_contains(ltrim((string) $response->getContent()), '<!DOCTYPE html')
            || str_contains(ltrim((string) $response->getContent()), '<html');
    }
}
