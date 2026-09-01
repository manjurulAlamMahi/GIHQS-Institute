@extends('backend.app')

@section('title', 'Api Settings')

@section('content')
    @php
        $maskSecret = function ($value, $prefixLength = 8, $suffixLength = 4) {
            if (empty($value)) {
                return '';
            }
            
            $length = strlen($value);
            if ($length <= ($prefixLength + $suffixLength)) {
                return str_repeat('•', 20);
            }
            
            $prefix = substr($value, 0, $prefixLength);
            $suffix = substr($value, -$suffixLength);
            $maskLength = max(12, $length - ($prefixLength + $suffixLength));
            
            return $prefix . str_repeat('•', $maskLength) . $suffix;
        };
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Api Settings</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Api Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title mb-0 flex-grow-1">Update Stripe & Classmarker Settings</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.api-settings.update') }}">
                        @csrf

                        <h5 class="fw-bold mb-3 text-primary">Stripe Settings</h5>

                        <div class="form-group mb-3">
                            <label for="stripe_public_key" class="form-label">Stripe Public Key</label>
                            <input type="text" name="stripe_public_key" id="stripe_public_key" class="form-control @error('stripe_public_key') is-invalid @enderror"
                                value="{{ $maskSecret(config('services.stripe.key'), 9, 2) }}" placeholder="Enter Stripe Public Key">
                            @error('stripe_public_key')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="stripe_secret_key" class="form-label">Stripe Secret Key</label>
                            <input type="text" name="stripe_secret_key" id="stripe_secret_key" class="form-control @error('stripe_secret_key') is-invalid @enderror"
                                value="{{ $maskSecret(config('services.stripe.secret'), 9, 2) }}" placeholder="Enter Stripe Secret Key">
                            @error('stripe_secret_key')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="stripe_webhook_secret" class="form-label">Stripe Webhook Secret</label>
                            <input type="text" name="stripe_webhook_secret" id="stripe_webhook_secret" class="form-control @error('stripe_webhook_secret') is-invalid @enderror"
                                value="{{ $maskSecret(config('services.stripe.webhook_secret'), 6, 2) }}" placeholder="Enter Stripe Webhook Secret">
                            @error('stripe_webhook_secret')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3 text-primary">Classmarker Settings</h5>

                        <div class="form-group mb-3">
                            <label for="classmarker_api_key" class="form-label">Classmarker API Key</label>
                            <input type="text" name="classmarker_api_key" id="classmarker_api_key" class="form-control @error('classmarker_api_key') is-invalid @enderror"
                                value="{{ $maskSecret(config('services.classmarker.api_key'), 4, 2) }}" placeholder="Enter Classmarker API Key">
                            @error('classmarker_api_key')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="classmarker_api_secret" class="form-label">Classmarker API Secret</label>
                            <input type="text" name="classmarker_api_secret" id="classmarker_api_secret" class="form-control @error('classmarker_api_secret') is-invalid @enderror"
                                value="{{ $maskSecret(config('services.classmarker.api_secret'), 4, 2) }}" placeholder="Enter Classmarker API Secret">
                            @error('classmarker_api_secret')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="classmarker_base_url" class="form-label">Classmarker Base URL</label>
                            <input type="text" name="classmarker_base_url" id="classmarker_base_url" class="form-control @error('classmarker_base_url') is-invalid @enderror"
                                value="{{ config('services.classmarker.api_url') }}" placeholder="Enter Classmarker Base URL">
                            @error('classmarker_base_url')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="classmarker_webhook_secret" class="form-label">Classmarker Webhook Secret</label>
                            <input type="text" name="classmarker_webhook_secret" id="classmarker_webhook_secret" class="form-control @error('classmarker_webhook_secret') is-invalid @enderror"
                                value="{{ $maskSecret(config('services.classmarker.webhook_secret'), 4, 2) }}" placeholder="Enter Classmarker Webhook Secret">
                            @error('classmarker_webhook_secret')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3 text-primary">Ai Api Settings</h5>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="ai_pathway_wizard_enable" id="ai_pathway_wizard_enable" value="1"
                                {{ config('services.ai.pathway_wizard_enable') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="ai_pathway_wizard_enable">Enable AI for Pathway Wizard</label>
                            <small class="form-text text-muted d-block">If enabled, the pathway results will be dynamically generated by the configured AI. If disabled or if AI fails/is not configured, it will fall back to the manual static structure.</small>
                        </div>

                        <h6 class="fw-semibold text-secondary mb-3">Primary AI</h6>

                        <div class="form-group mb-3">
                            <label for="ai_primary_provider" class="form-label">Primary AI Provider</label>
                            <input type="text" name="ai_primary_provider" id="ai_primary_provider" class="form-control @error('ai_primary_provider') is-invalid @enderror"
                                value="{{ config('services.ai.primary.provider') }}" placeholder="e.g. openai">
                            @error('ai_primary_provider')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="ai_primary_api_key" class="form-label">Primary AI API Key</label>
                            <input type="text" name="ai_primary_api_key" id="ai_primary_api_key" class="form-control @error('ai_primary_api_key') is-invalid @enderror"
                                value="{{ $maskSecret(config('services.ai.primary.api_key'), 8, 2) }}" placeholder="Enter Primary AI API Key">
                            @error('ai_primary_api_key')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="ai_primary_model" class="form-label">Primary AI Model</label>
                            <input type="text" name="ai_primary_model" id="ai_primary_model" class="form-control @error('ai_primary_model') is-invalid @enderror"
                                value="{{ config('services.ai.primary.model') }}" placeholder="e.g. gpt-4">
                            @error('ai_primary_model')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <h6 class="fw-semibold text-secondary mb-3 mt-4">Fallback AI</h6>

                        <div class="form-group mb-3">
                            <label for="ai_fallback_provider" class="form-label">Fallback AI Provider</label>
                            <input type="text" name="ai_fallback_provider" id="ai_fallback_provider" class="form-control @error('ai_fallback_provider') is-invalid @enderror"
                                value="{{ config('services.ai.fallback.provider') }}" placeholder="e.g. gemini">
                            @error('ai_fallback_provider')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="ai_fallback_api_key" class="form-label">Fallback AI API Key</label>
                            <input type="text" name="ai_fallback_api_key" id="ai_fallback_api_key" class="form-control @error('ai_fallback_api_key') is-invalid @enderror"
                                value="{{ $maskSecret(config('services.ai.fallback.api_key'), 8, 2) }}" placeholder="Enter Fallback AI API Key">
                            @error('ai_fallback_api_key')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="ai_fallback_model" class="form-label">Fallback AI Model</label>
                            <input type="text" name="ai_fallback_model" id="ai_fallback_model" class="form-control @error('ai_fallback_model') is-invalid @enderror"
                                value="{{ config('services.ai.fallback.model') }}" placeholder="e.g. gemini-1.5-pro">
                            @error('ai_fallback_model')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">Update Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
