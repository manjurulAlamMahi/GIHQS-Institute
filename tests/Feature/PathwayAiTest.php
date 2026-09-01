<?php

use App\Models\PathwayQuestion;
use App\Models\PathwayOption;
use App\Models\PathwayResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    // Clean up
    PathwayOption::query()->delete();
    PathwayQuestion::query()->delete();
    PathwayResult::query()->delete();

    // Create a result
    $this->result = PathwayResult::create([
        'title' => 'Static Title',
        'description' => 'Static Description',
        'badges' => ['Static'],
        'info_box_text' => 'Static Info Box',
        'primary_button_text' => 'Static Primary',
        'primary_button_url' => '/static/primary',
        'secondary_button_text' => 'Static Secondary',
        'secondary_button_url' => '/static/secondary',
        'status' => 1,
    ]);

    // Create questions and options
    $this->q1 = PathwayQuestion::create([
        'step_number' => 1,
        'question_text' => 'Role?',
        'status' => 1,
    ]);

    $this->q2 = PathwayQuestion::create([
        'step_number' => 2,
        'question_text' => 'Objective?',
        'status' => 1,
    ]);

    $this->opt1 = PathwayOption::create([
        'question_id' => $this->q1->id,
        'option_text' => 'Individual',
        'next_question_id' => $this->q2->id,
        'order' => 1,
        'status' => 1,
    ]);

    $this->opt2 = PathwayOption::create([
        'question_id' => $this->q2->id,
        'option_text' => 'Accreditation',
        'next_question_id' => null,
        'result_id' => $this->result->id,
        'order' => 1,
        'status' => 1,
    ]);
});

it('returns static result directly if pathway wizard AI is disabled', function () {
    Config::set('services.ai.pathway_wizard_enable', false);

    $response = $this->getJson("/api/pathways/step/{$this->opt2->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'type' => 'result',
            'data' => [
                'id' => $this->result->id,
                'title' => 'Static Title',
                'description' => 'Static Description',
            ]
        ]);
});

it('returns static result directly if AI is enabled but not configured', function () {
    Config::set('services.ai.pathway_wizard_enable', true);
    Config::set('services.ai.primary.provider', null);
    Config::set('services.ai.primary.api_key', null);
    Config::set('services.ai.fallback.provider', null);
    Config::set('services.ai.fallback.api_key', null);

    $response = $this->getJson("/api/pathways/step/{$this->opt2->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'type' => 'result',
            'data' => [
                'id' => $this->result->id,
                'title' => 'Static Title',
            ]
        ]);
});

it('selects best result using OpenAI when enabled and configured', function () {
    Config::set('services.ai.pathway_wizard_enable', true);
    Config::set('services.ai.primary.provider', 'openai');
    Config::set('services.ai.primary.api_key', 'test-openai-key');
    Config::set('services.ai.primary.model', 'gpt-4');

    Http::fake([
        'api.openai.com/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'best_result_id' => $this->result->id
                        ])
                    ]
                ]
            ]
        ], 200)
    ]);

    $response = $this->getJson("/api/pathways/step/{$this->opt2->id}?history={$this->opt1->id},{$this->opt2->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'type' => 'result',
            'data' => [
                'id' => $this->result->id,
                'title' => 'Static Title',
                'description' => 'Static Description',
                'badges' => ['Static'],
                'info_box_text' => 'Static Info Box',
                'primary_button_text' => 'Static Primary',
                'primary_button_url' => '/static/primary',
                'secondary_button_text' => 'Static Secondary',
                'secondary_button_url' => '/static/secondary',
            ]
        ]);
});

it('falls back to Gemini if OpenAI fails', function () {
    Config::set('services.ai.pathway_wizard_enable', true);
    Config::set('services.ai.primary.provider', 'openai');
    Config::set('services.ai.primary.api_key', 'test-openai-key');
    Config::set('services.ai.fallback.provider', 'gemini');
    Config::set('services.ai.fallback.api_key', 'test-gemini-key');
    Config::set('services.ai.fallback.model', 'gemini-1.5-flash');

    Http::fake([
        'api.openai.com/*' => Http::response([], 500),
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            [
                                'text' => json_encode([
                                    'best_result_id' => $this->result->id
                                    ])
                            ]
                        ]
                    ]
                ]
            ]
        ], 200)
    ]);

    $response = $this->getJson("/api/pathways/step/{$this->opt2->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'type' => 'result',
            'data' => [
                'id' => $this->result->id,
                'title' => 'Static Title',
                'description' => 'Static Description',
            ]
        ]);
});

it('falls back to static database result if both OpenAI and Gemini fail', function () {
    Config::set('services.ai.pathway_wizard_enable', true);
    Config::set('services.ai.primary.provider', 'openai');
    Config::set('services.ai.primary.api_key', 'test-openai-key');
    Config::set('services.ai.fallback.provider', 'gemini');
    Config::set('services.ai.fallback.api_key', 'test-gemini-key');

    Http::fake([
        'api.openai.com/*' => Http::response([], 500),
        'generativelanguage.googleapis.com/*' => Http::response([], 500)
    ]);

    $response = $this->getJson("/api/pathways/step/{$this->opt2->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'type' => 'result',
            'data' => [
                'id' => $this->result->id,
                'title' => 'Static Title',
                'description' => 'Static Description',
            ]
        ]);
});
