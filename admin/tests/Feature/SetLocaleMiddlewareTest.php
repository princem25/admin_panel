<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetLocaleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sets_the_locale_from_the_session()
    {
        // 1. Visit the language switch route to set the session
        $response = $this->get(route('language.switch', 'ar'));
        $response->assertRedirect();
        $this->assertEquals('ar', session('locale'));

        // 2. Visit any page and check if the locale is applied
        $response = $this->withSession(['locale' => 'ar'])->get('/');
        $this->assertEquals('ar', app()->getLocale());
    }

    /** @test */
    public function it_sets_the_locale_from_the_authenticated_user_preference()
    {
        $user = \App\Models\User::factory()->create(['preferred_locale' => 'ar']);
        
        $this->actingAs($user);
        
        $response = $this->get('/');
        $this->assertEquals('ar', app()->getLocale());
        $this->assertEquals('ar', \Carbon\Carbon::getLocale());
    }

    /** @test */
    public function user_preference_takes_precedence_over_session()
    {
        $user = \App\Models\User::factory()->create(['preferred_locale' => 'ar']);
        $this->actingAs($user);

        // Even with session set to 'en', it should pick 'ar' from user DB
        $response = $this->withSession(['locale' => 'en'])->get('/');
        $this->assertEquals('ar', app()->getLocale());
    }

    /** @test */
    public function it_defaults_to_english_if_no_session_or_user_pref_exists()
    {
        $response = $this->get('/');
        $this->assertEquals('en', app()->getLocale());
    }
}
