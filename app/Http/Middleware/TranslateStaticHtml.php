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

        $locale = App::getLocale();
        if ($locale !== 'ar' || ! $this->isHtmlResponse($response)) {
            return $response;
        }

        $translations = trans('static');

        if (! is_array($translations) || empty($translations)) {
            return $response;
        }

        uksort($translations, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));
        $original = $response->getContent();
        $translated = strtr($original, $translations);
        $response->setContent($translated);

        return $response;
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
