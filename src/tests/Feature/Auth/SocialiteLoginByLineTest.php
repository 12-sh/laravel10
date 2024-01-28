<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SocialiteLoginByLineTest extends TestCase
{
    public function testLineCanRedirectToProvide(): void
    {
        $response = $this
            ->from('/login')
            ->get('/socialite/line');
        $response->assertStatus(302);
        $response->assertRedirect(config('service.line.redirect'));
    }

    public function testOtherShouldRedirectTo404(): void
    {
        $response = $this
            ->from('/login')
            ->get('/socialite/other');
        $response->assertStatus(404);
    }

    public function testLineCanGetSocialiteUserDataToConfirmation() : void
    {
        Artisan::call('db:seed SocialSeeder');
        $response = $this->from(config('services.line.redirect'))
            ->get('/socialite/line/callback', [
                'code' => 'abcd1234',
                'state' => '0987poi',
                'friendship_status_changed' => 'true',
            ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('socialite.confirmation',[
            'social' => 'line'
        ]));
    }

    public function testCallbackOtherShouldRedirectTo404(): void
    {
        $response = $this->get('/socialite/other/callback');
        $response->assertStatus(404);
    }

    public function testCallbackLineErrorShouldRedirectToLoginScreen() : void
    {
        Artisan::call('db:seed SocialSeeder');
        $response = $this->get('/socialite/line/callback',[
            'error' => 'access_denied',
            'error_description' => 'The+resource+owner+denied+the+request.',
            'state' => '0987poi',
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    public function testLineLoginScreenCanBeConfirmation() : void
    {
        Artisan::call('db:seed SocialSeeder');
        session()->put('socialiteUser', [
            'id' => fake()->md5(),
            'email' => fake()->email(),
            'name' => fake()->userName()
        ]);

        $response = $this->get('/socialite/line/confirmation');
        $response->assertStatus(200);
    }
}
