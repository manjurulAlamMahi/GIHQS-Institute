<?php

namespace App\Http\Controllers\Backend\Setting;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class ApiSettingController extends Controller
{
    public function edit()
    {
        return view('backend.layouts.settings.api_settings');
    }

    public function update(Request $request)
    {
        $request->validate([
            'stripe_public_key'          => 'nullable|string|max:255',
            'stripe_secret_key'          => 'nullable|string|max:255',
            'stripe_webhook_secret'      => 'nullable|string|max:255',
            'classmarker_api_key'        => 'nullable|string|max:255',
            'classmarker_api_secret'      => 'nullable|string|max:255',
            'classmarker_base_url'        => 'nullable|string|max:255',
            'classmarker_webhook_secret'  => 'nullable|string|max:255',
            'ai_primary_provider'        => 'nullable|string|max:255',
            'ai_primary_api_key'         => 'nullable|string|max:255',
            'ai_primary_model'           => 'nullable|string|max:255',
            'ai_fallback_provider'       => 'nullable|string|max:255',
            'ai_fallback_api_key'        => 'nullable|string|max:255',
            'ai_fallback_model'          => 'nullable|string|max:255',
            'ai_pathway_wizard_enable'   => 'nullable|boolean',
        ]);

        try {
            $data = [];
            $fields = [
                'STRIPE_PUBLIC_KEY'          => 'stripe_public_key',
                'STRIPE_SECRET_KEY'          => 'stripe_secret_key',
                'STRIPE_WEBHOOK_SECRET'      => 'stripe_webhook_secret',
                'CLASSMARKER_API_KEY'        => 'classmarker_api_key',
                'CLASSMARKER_API_SECRET'      => 'classmarker_api_secret',
                'CLASSMARKER_WEBHOOK_SECRET'  => 'classmarker_webhook_secret',
                'AI_PRIMARY_API_KEY'         => 'ai_primary_api_key',
                'AI_FALLBACK_API_KEY'        => 'ai_fallback_api_key',
            ];

            foreach ($fields as $envKey => $inputName) {
                $inputValue = $request->input($inputName);

                if (empty($inputValue)) {
                    $data[$envKey] = '';
                } elseif (str_contains($inputValue, '••••')) {
                    // Value is masked, meaning it was not modified. Read existing raw value from .env directly (env() returns null when config is cached)
                    $data[$envKey] = $this->getExistingEnvValue($envKey);
                } else {
                    // Value is new. Encrypt it before saving.
                    $data[$envKey] = encrypt($inputValue);
                }
            }

            // Save non-secret parameters as is
            $data['CLASSMARKER_BASE_URL'] = $request->classmarker_base_url;
            $data['AI_PRIMARY_PROVIDER']  = $request->ai_primary_provider;
            $data['AI_PRIMARY_MODEL']     = $request->ai_primary_model;
            $data['AI_FALLBACK_PROVIDER'] = $request->ai_fallback_provider;
            $data['AI_FALLBACK_MODEL']   = $request->ai_fallback_model;
            $data['AI_PATHWAY_WIZARD_ENABLE'] = $request->has('ai_pathway_wizard_enable') ? 'true' : 'false';

            $this->updateEnv($data);

            // Clear config cache so live server immediately picks up .env changes
            try {
                Artisan::call('config:clear');
            } catch (\Throwable $e) {
                // Ignore if artisan command fails in restricted server environment
            }

            return back()->with('success', 'API settings updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update API settings: ' . $e->getMessage());
        }
    }

    protected function getExistingEnvValue(string $envKey): string
    {
        $envPath = base_path('.env');
        if (!File::exists($envPath)) {
            return '';
        }

        $envContent = File::get($envPath);
        if (preg_match("/^{$envKey}=(.*)$/m", $envContent, $matches)) {
            $val = trim($matches[1]);
            return trim($val, '"\'');
        }

        return '';
    }

    protected function updateEnv(array $data)
    {
        $envPath = base_path('.env');
        if (!File::exists($envPath)) {
            return;
        }

        $envContent = File::get($envPath);

        foreach ($data as $key => $value) {
            $value = trim($value);
            // Double quote values containing special characters or long encrypted strings to prevent parsing issues
            if (str_contains($value, ' ') || str_contains($value, '=') || strlen($value) > 50) {
                $value = '"' . str_replace('"', '\"', $value) . '"';
            }

            // Check if key exists
            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        File::put($envPath, $envContent);
    }
}
