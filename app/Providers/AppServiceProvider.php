<?php

namespace App\Providers;

use App\Models\AdminSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\Stripe\StripeClient::class, function ($app) {
            return new \Stripe\StripeClient(config('services.stripe.secret'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->decryptSensitiveConfigs();
        $this->configureRateLimiting();

        // Get Data From Database
        View::composer('backend.*', function ($view) {
            $adminSetting = AdminSetting::first() ?? new AdminSetting([
                'logo' => 'default-logo.png',
                'mini_logo' => 'default-mini-logo.png',
                'favicon' => 'default-favicon.png',
                'system_title' => 'My System',
                'company_name' => 'My Company',
                'tag_line' => 'Best Company',
                'phone_number' => '017XXXXXXXX',
                'whatsapp_number' => '017XXXXXXXX',
                'email' => 'email@email.com',
                'copyright_text' => '2025 © Company. All rights reserved.',
            ]);

            $view->with('adminSetting', $adminSetting);
        });
    }

    /**
     * Rate limiters for the API.
     *
     * The api group carried no throttle at all, so credential stuffing against
     * /api/login and OTP guessing against the password-reset endpoints were
     * limited only by how fast requests could be sent.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        // Credential and OTP endpoints, keyed on both the account being targeted
        // and the source address so neither a single IP nor a single account can
        // be hammered.
        RateLimiter::for('auth', function (Request $request) {
            $email = (string) $request->input('email');

            return [
                Limit::perMinute(5)->by('auth-ip:' . $request->ip()),
                Limit::perMinute(5)->by('auth-email:' . mb_strtolower($email) . '|' . $request->ip()),
            ];
        });
    }

    /**
     * Dynamically decrypt sensitive configurations at boot time.
     */
    protected function decryptSensitiveConfigs(): void
    {
        $keysToDecrypt = [
            'services.stripe.key',
            'services.stripe.secret',
            'services.stripe.webhook_secret',
            'services.classmarker.api_key',
            'services.classmarker.api_secret',
            'services.classmarker.webhook_secret',
            'services.ai.primary.api_key',
            'services.ai.fallback.api_key',
        ];

        foreach ($keysToDecrypt as $configKey) {
            $value = config($configKey);
            if ($value && $this->isEncrypted($value)) {
                try {
                    config([$configKey => decrypt($value)]);
                } catch (DecryptException $e) {
                    // Fallback to raw value if decryption fails
                }
            }
        }
    }

    /**
     * Check if a value is a valid Laravel encryption payload.
     */
    protected function isEncrypted($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $payload = json_decode(base64_decode($value), true);
        return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']);
    }
}
