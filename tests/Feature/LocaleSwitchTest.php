<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->get('/__locale-test', function () {
            return response(app()->getLocale());
        });
    }

    public function test_it_defaults_to_tr_when_browser_locale_is_not_supported(): void
    {
        $response = $this->withHeader('Accept-Language', 'de-DE,de;q=0.9')->get('/__locale-test');

        $response->assertOk();
        $response->assertSeeText('tr');
    }

    public function test_it_detects_browser_english_locale_on_first_visit(): void
    {
        $response = $this->withHeader('Accept-Language', 'en-US,en;q=0.9')->get('/__locale-test');

        $response->assertOk();
        $response->assertSeeText('en');
    }

    public function test_cookie_locale_overrides_browser_locale(): void
    {
        $response = $this
            ->withCookie('locale', 'tr')
            ->withHeader('Accept-Language', 'en-US,en;q=0.9')
            ->get('/__locale-test');

        $response->assertOk();
        $response->assertSeeText('tr');
    }

    public function test_locale_switch_route_sets_cookie_and_redirects_back(): void
    {
        $response = $this
            ->from('/')
            ->post(route('locale.switch', 'en'));

        $response->assertRedirect('/');
        $response->assertCookie('locale', 'en');
    }

    public function test_locale_switch_route_rejects_invalid_locale(): void
    {
        $response = $this->post(route('locale.switch', 'de'));

        $response->assertStatus(400);
    }
}
