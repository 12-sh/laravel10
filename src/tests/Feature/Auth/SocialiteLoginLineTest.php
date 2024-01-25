<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SocialiteLoginByLineTest extends TestCase
{
    public function testLineCanRedirectToProvide(): void
    {
        $response = $this->get('/socialite/line');
        $response->assertStatus(302);
    }

    public function testOtherShouldRedirectTo404(): void
    {
        $response = $this->get('/socialite/other');
        $response->assertStatus(404);
    }

    function testCallbackOtherShouldRedirectTo404(): void
    {
        $response = $this->get('/socialite/other/callback');
        $response->assertStatus(404);
    }

    public function testCallbackLineErrorShouldRedirectToLoginScreen() : void
    {
        $response = $this->get('/socialite/line/callback',[
            'error' => "invalid_request",
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }
}
