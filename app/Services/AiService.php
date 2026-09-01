<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    /**
     * Let AI select the best database result ID based on the selection path history.
     *
     * @param array $path History of user answers [ ['question' => '...', 'option' => '...'], ... ]
     * @param array $allResults List of all active database results
     * @param array $config Configuration for the provider (provider, api_key, model)
     * @return int|null The selected result ID or null if it fails
     */
    public function selectBestResultId(array $path, array $allResults, array $config): ?int
    {
        $provider = strtolower(trim($config['provider'] ?? ''));
        $apiKey = trim($config['api_key'] ?? '');
        $model = trim($config['model'] ?? '');

        if (empty($provider) || empty($apiKey)) {
            Log::warning("AI service called but provider or api_key is missing.");
            return null;
        }

        // Generate the prompt
        $prompt = $this->buildSelectionPrompt($path, $allResults);

        try {
            if ($provider === 'openai') {
                return $this->callOpenAi($prompt, $apiKey, $model);
            } elseif ($provider === 'gemini') {
                return $this->callGemini($prompt, $apiKey, $model);
            } else {
                Log::warning("Unsupported AI provider specified: {$provider}");
                return null;
            }
        } catch (\Throwable $e) {
            Log::error("Failed to select AI result with provider '{$provider}': " . $e->getMessage(), [
                'exception' => $e
            ]);
            return null;
        }
    }

    /**
     * Build prompt for the AI to select the best result ID.
     */
    protected function buildSelectionPrompt(array $path, array $allResults): string
    {
        $journeyStr = '';
        foreach ($path as $index => $step) {
            $num = $index + 1;
            $journeyStr .= "Step {$num}. Question: \"{$step['question']}\" -> Answer: \"{$step['option']}\"\n";
        }

        $resultsStr = '';
        foreach ($allResults as $res) {
            $badges = is_array($res['badges'] ?? null) ? implode(', ', $res['badges']) : ($res['badges'] ?? '');
            $resultsStr .= "Result ID: {$res['id']}\n";
            $resultsStr .= "Title: {$res['title']}\n";
            $resultsStr .= "Description: {$res['description']}\n";
            $resultsStr .= "Badges: {$badges}\n";
            $resultsStr .= "Info Box Text: " . ($res['info_box_text'] ?? 'None') . "\n";
            $resultsStr .= "---\n";
        }

        return "You are an expert AI selector for the Global Institute for Healthcare Quality and Safety (GIHQS).\n" .
            "Your task is to analyze the user's wizard selection journey and select the single best matching result option from our database.\n\n" .
            "User's Journey (Questions & Selected Options):\n" .
            $journeyStr . "\n" .
            "Available Database Results:\n" .
            $resultsStr . "\n" .
            "Based on the User's Journey, determine which Result ID is the most relevant and best fits the user's needs.\n\n" .
            "You MUST return a valid JSON object matching the schema below. Do not output any HTML, markdown code block backticks, or explanatory text. Return ONLY the raw JSON object.\n\n" .
            "JSON Schema:\n" .
            "{\n" .
            "    \"best_result_id\": 123\n" .
            "}";
    }

    /**
     * Call OpenAI API.
     */
    protected function callOpenAi(string $prompt, string $apiKey, string $model): ?int
    {
        $model = !empty($model) ? $model : 'gpt-4o-mini';

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that outputs only valid raw JSON matching the requested schema.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'response_format' => ['type' => 'json_object']
        ]);

        if ($response->failed()) {
            Log::error("OpenAI request failed: Status {$response->status()}, Body: " . $response->body());
            return null;
        }

        $data = $response->json();
        $text = $data['choices'][0]['message']['content'] ?? '';
        
        return $this->parseAndValidateJson($text);
    }

    /**
     * Call Gemini API.
     */
    protected function callGemini(string $prompt, string $apiKey, string $model): ?int
    {
        $model = !empty($model) ? $model : 'gemini-1.5-flash';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(15)->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json'
            ]
        ]);

        if ($response->failed()) {
            Log::error("Gemini request failed: Status {$response->status()}, Body: " . $response->body());
            return null;
        }

        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        return $this->parseAndValidateJson($text);
    }

    /**
     * Helper to parse, validate, and structure the JSON response.
     */
    protected function parseAndValidateJson(string $jsonText): ?int
    {
        $jsonText = trim($jsonText);

        // Strip markdown code block wrappers if the AI included them anyway
        if (str_starts_with($jsonText, '```')) {
            $jsonText = preg_replace('/^```(?:json)?/i', '', $jsonText);
            $jsonText = preg_replace('/```$/', '', $jsonText);
            $jsonText = trim($jsonText);
        }

        $decoded = json_decode($jsonText, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("AI returned invalid JSON content: " . json_last_error_msg() . "\nContent: " . $jsonText);
            return null;
        }

        if (!isset($decoded['best_result_id'])) {
            Log::warning("AI response missing key: best_result_id");
            return null;
        }

        return (int)$decoded['best_result_id'];
    }
}
