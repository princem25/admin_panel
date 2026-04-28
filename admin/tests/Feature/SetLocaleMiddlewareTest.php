<?php

namespace Tests\Feature;

use Tests\TestCase;

class SetLocaleMiddlewareTest extends TestCase
{
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
    public function it_defaults_to_english_if_no_session_exists()
    {
        $response = $this->get('/');
        $this->assertEquals('en', app()->getLocale());
    }
}
