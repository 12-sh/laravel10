<?php

namespace App\Providers\Socialite;

use App\Providers\Socialite\Test\line\TestProvider;
use SocialiteProviders\Line\LineExtendSocialite as LineLineExtendSocialite;
use SocialiteProviders\Line\Provider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class LineExtendSocialite extends LineLineExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(
        SocialiteWasCalled $socialiteWasCalled
    )
    {
        if (config('app.env') === 'local') {
            $socialiteWasCalled->extendSocialite('line', TestProvider::class);
        } else {
            $socialiteWasCalled->extendSocialite('line', Provider::class);
        }
    }
}
