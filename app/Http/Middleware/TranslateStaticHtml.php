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

        $translations = trans('static');

        if (! is_array($translations) || empty($translations)) {
            return $response;
        }

        uksort($translations, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));
        $response->setContent(strtr($response->getContent(), $translations));

        return $response;
    }

    private function isHtmlResponse(Response $response): bool
    {
        $contentType = (string) $response->headers->get('Content-Type');

        return $response->isSuccessful()
            && method_exists($response, 'getContent')
            && str_contains($contentType, 'text/html');
    }
}
