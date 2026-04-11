<?php

namespace Tests\Feature;

use Tests\TestCase;

class LanguagePickerTest extends TestCase
{
    public function test_picker_renders_when_seen_cookie_is_absent(): void
    {
        $response = $this->get('/en/');
        $response->assertStatus(200);
        $response->assertSee('id="lang-picker"', false);
        $response->assertSee('Choose your language', false);
        $response->assertSee('اختر لغتك', false);
        $response->assertSee('Scegli la tua lingua', false);
    }

    public function test_picker_hidden_when_seen_cookie_is_present(): void
    {
        $response = $this->withCookie('delos_locale_seen', '1')->get('/en/');
        $response->assertStatus(200);
        $response->assertDontSee('id="lang-picker"', false);
    }

    public function test_picker_cards_link_to_all_three_locales(): void
    {
        $response = $this->get('/en/about');
        $response->assertSee('href="/en/about"', false);
        $response->assertSee('href="/ar/about"', false);
        $response->assertSee('href="/it/about"', false);
    }

    public function test_locale_cookie_is_refreshed_on_every_request(): void
    {
        $response = $this->withCookie('delos_locale_seen', '1')->get('/en/');
        $response->assertCookie('delos_locale', 'en');
    }

    public function test_picker_heading_renders_in_all_three_languages_regardless_of_page_locale(): void
    {
        // When visiting the Italian page, the picker should STILL show all three
        // language labels so every visitor can recognize their language.
        $response = $this->get('/it/');
        $response->assertSee('Choose your language', false);
        $response->assertSee('اختر لغتك', false);
        $response->assertSee('Scegli la tua lingua', false);
    }

    public function test_language_switcher_is_present_in_all_three_variants(): void
    {
        $response = $this->withCookie('delos_locale_seen', '1')->get('/en/');
        // Desktop dropdown
        $response->assertSee('data-lang-dropdown-toggle', false);
        // Mobile drawer links + footer links (multiple data-language-switch attrs)
        $this->assertGreaterThanOrEqual(
            3,
            substr_count($response->getContent(), 'data-language-switch='),
            'Expected at least 3 data-language-switch occurrences (desktop + mobile + footer)'
        );
    }
}
