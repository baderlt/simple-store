<?php

namespace Tests\Feature;

use App\Http\Middleware\TranslateStaticHtml;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ArabicLocalizationTest extends TestCase
{
    public function test_arabic_static_translation_only_changes_visible_text_and_supported_attributes(): void
    {
        App::setLocale('ar');
        $request = Request::create('/produits');
        $response = (new TranslateStaticHtml)->handle($request, fn () => new Response(
            '<!DOCTYPE html><html><body><a href="/produits" class="produits" title="Voir le produit">Produits</a>'
            .'<input placeholder="Rechercher une catégorie...">'
            .'<script>const label = "Produits"; const url = "/produits";</script></body></html>',
            headers: ['Content-Type' => 'text/html']
        ));

        $content = $response->getContent();

        $this->assertStringContainsString('>المنتجات</a>', $content);
        $this->assertStringContainsString('title="عرض المنتج"', $content);
        $this->assertStringContainsString('placeholder="ابحث عن فئة..."', $content);
        $this->assertStringContainsString('href="/produits"', $content);
        $this->assertStringContainsString('class="produits"', $content);
        $this->assertStringContainsString('const label = "Produits"', $content);
    }

    public function test_non_arabic_html_is_not_modified(): void
    {
        App::setLocale('fr');
        $request = Request::create('/produits');
        $response = (new TranslateStaticHtml)->handle(
            $request,
            fn () => new Response('<!DOCTYPE html><p>Produits</p>', headers: ['Content-Type' => 'text/html'])
        );

        $this->assertStringContainsString('<p>Produits</p>', $response->getContent());
    }
}
