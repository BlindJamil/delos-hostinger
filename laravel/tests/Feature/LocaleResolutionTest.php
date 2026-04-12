<?php

namespace Tests\Feature;

use App\Support\LocaleResolver;
use Illuminate\Http\Request;
use Tests\TestCase;

class LocaleResolutionTest extends TestCase
{
    public function test_resolves_locale_from_route_segment_with_highest_priority(): void
    {
        $resolver = new LocaleResolver();
        $request = Request::create('/ar/services', 'GET');
        $request->setRouteResolver(fn () => new class {
            public function parameter($key) { return $key === 'locale' ? 'ar' : null; }
        });

        $this->assertSame('ar', $resolver->resolve($request));
    }

    public function test_falls_back_to_cookie_when_no_route_segment(): void
    {
        $resolver = new LocaleResolver();
        $request = Request::create('/', 'GET', [], ['delos_locale' => 'it']);

        $this->assertSame('it', $resolver->resolve($request));
    }

    public function test_falls_back_to_accept_language_when_no_cookie(): void
    {
        $resolver = new LocaleResolver();
        $request = Request::create('/', 'GET');
        $request->headers->set('Accept-Language', 'ar-IQ,en;q=0.9');

        $this->assertSame('ar', $resolver->resolve($request));
    }

    public function test_respects_accept_language_q_values(): void
    {
        $resolver = new LocaleResolver();
        $request = Request::create('/', 'GET');
        $request->headers->set('Accept-Language', 'fr;q=1.0,it;q=0.9,en;q=0.5');

        // fr not supported, it has highest supported quality
        $this->assertSame('it', $resolver->resolve($request));
    }

    public function test_defaults_to_english_when_nothing_matches(): void
    {
        $resolver = new LocaleResolver();
        $request = Request::create('/', 'GET');

        $this->assertSame('en', $resolver->resolve($request));
    }

    public function test_rejects_invalid_cookie_value(): void
    {
        $resolver = new LocaleResolver();
        $request = Request::create('/', 'GET', [], ['delos_locale' => 'hack']);

        $this->assertSame('en', $resolver->resolve($request));
    }

    public function test_is_rtl_only_for_arabic(): void
    {
        $resolver = new LocaleResolver();

        $this->assertTrue($resolver->isRtl('ar'));
        $this->assertFalse($resolver->isRtl('en'));
        $this->assertFalse($resolver->isRtl('it'));
    }

    public function test_dir_attribute_matches_locale(): void
    {
        $resolver = new LocaleResolver();

        $this->assertSame('rtl', $resolver->dir('ar'));
        $this->assertSame('ltr', $resolver->dir('en'));
        $this->assertSame('ltr', $resolver->dir('it'));
    }

    public function test_urls_for_path_builds_per_locale_map(): void
    {
        $resolver = new LocaleResolver();

        $urls = $resolver->urlsForPath('en/services');
        $this->assertSame('/en/services', $urls['en']);
        $this->assertSame('/ar/services', $urls['ar']);
        $this->assertSame('/it/services', $urls['it']);
    }

    public function test_urls_for_path_handles_root_path(): void
    {
        $resolver = new LocaleResolver();

        $urls = $resolver->urlsForPath('en');
        $this->assertSame('/en', $urls['en']);
        $this->assertSame('/ar', $urls['ar']);
        $this->assertSame('/it', $urls['it']);
    }

    public function test_root_redirects_to_resolved_locale(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/en');
    }

    public function test_root_redirects_to_cookie_locale_when_set(): void
    {
        // delos_locale is excluded from cookie encryption (see bootstrap/app.php),
        // so the test helper must send it unencrypted to match production behavior.
        $response = $this->withUnencryptedCookie('delos_locale', 'ar')->get('/');
        $response->assertRedirect('/ar');
    }

    public function test_en_services_page_renders_with_ltr_direction(): void
    {
        $response = $this->get('/en/services');
        $response->assertStatus(200);
        $response->assertSee('lang="en"', false);
        $response->assertSee('dir="ltr"', false);
        $response->assertSee('Italian Kitchens', false);
    }

    public function test_ar_services_page_renders_with_rtl_direction(): void
    {
        $response = $this->get('/ar/services');
        $response->assertStatus(200);
        $response->assertSee('lang="ar"', false);
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('المطابخ الإيطالية', false);
    }

    public function test_it_services_page_renders_in_italian(): void
    {
        $response = $this->get('/it/services');
        $response->assertStatus(200);
        $response->assertSee('lang="it"', false);
        $response->assertSee('Cucine italiane', false);
    }

    public function test_hreflang_alternates_appear_on_all_pages(): void
    {
        $response = $this->get('/en/services');
        $response->assertSee('hreflang="en"', false);
        $response->assertSee('hreflang="ar-IQ"', false);
        $response->assertSee('hreflang="it"', false);
        $response->assertSee('hreflang="x-default"', false);
    }

    public function test_legacy_unprefixed_url_redirects_to_resolved_locale(): void
    {
        $response = $this->get('/services');
        $response->assertRedirect('/en/services');
    }
}
