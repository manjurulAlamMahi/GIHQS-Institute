<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PathwayQuestion;
use App\Models\PathwayOption;
use App\Models\PathwayResult;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PathwayApiController extends Controller
{
    /**
     * Start the pathway wizard (Get Step 1 Question with options).
     */
    public function start()
    {
        $question = PathwayQuestion::where('step_number', 1)
            ->where('status', 1)
            ->with(['options' => function($query) {
                $query->where('status', 1)->orderBy('order');
            }])
            ->first();

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'No active starting question found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'type' => 'question',
            'data' => $question
        ]);
    }

    /**
     * Submit choice / Get next step.
     * Receives option_id and returns next question OR final result.
     */
    public function getNextStep($optionId)
    {
        $option = PathwayOption::where('status', 1)->find($optionId);

        if (!$option) {
            return response()->json([
                'success' => false,
                'message' => 'Selected option not found or inactive.'
            ], 404);
        }

        // Case 1: Option points to another question (e.g. going to Step 2 or Step 3)
        if ($option->next_question_id) {
            $nextQuestion = PathwayQuestion::where('status', 1)
                ->with(['options' => function($query) {
                    $query->where('status', 1)->orderBy('order');
                }])
                ->find($option->next_question_id);

            if (!$nextQuestion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Next question not found or inactive.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'type' => 'question',
                'data' => $nextQuestion
            ]);
        }

        // Case 2: Option points directly to a final result
        if ($option->result_id) {
            $result = PathwayResult::where('status', 1)->find($option->result_id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Result not found or inactive.'
                ], 404);
            }

            // Check if AI is enabled for the Pathway Wizard
            if (config('services.ai.pathway_wizard_enable')) {
                Log::info('[Pathway Wizard] AI is enabled. Attempting dynamic result selection.');
                // Try to let AI select the best database result dynamically based on selection history
                $aiSelectedResult = $this->decideBestAiResult($option);
                if ($aiSelectedResult) {
                    Log::info('[Pathway Wizard] Successfully returned AI-selected result.', ['result_id' => $aiSelectedResult->id]);
                    return response()->json([
                        'success' => true,
                        'type' => 'result',
                        'data' => $aiSelectedResult
                    ]);
                }
                Log::warning('[Pathway Wizard] AI failed to select a result. Falling back to default static result.');
            } else {
                Log::info('[Pathway Wizard] AI is disabled. Using default static result.');
            }

            return response()->json([
                'success' => true,
                'type' => 'result',
                'data' => $result
            ]);
        }

        // Case 3: Option does not point to anything
        return response()->json([
            'success' => true,
            'type' => 'none',
            'message' => 'End of pathway. No further questions or results configured.'
        ]);
    }

    /**
     * Reconstruct the user selection path leading to the given option.
     */
    protected function reconstructSelectionPath($optionId)
    {
        $path = [];

        // Check if history is provided in the request query parameter
        $historyStr = request()->query('history');
        if (!empty($historyStr)) {
            $historyIds = array_filter(array_map('intval', explode(',', $historyStr)));
            
            // Ensure the current optionId is included at the end of history if not already there
            if (!in_array((int)$optionId, $historyIds)) {
                $historyIds[] = (int)$optionId;
            }

            $options = PathwayOption::whereIn('id', $historyIds)->with('question')->get()->sortBy(function($opt) use ($historyIds) {
                return array_search($opt->id, $historyIds);
            });

            foreach ($options as $opt) {
                $path[] = [
                    'question' => $opt->question ? $opt->question->question_text : '',
                    'option' => $opt->option_text
                ];
            }
        }

        // Fallback: If no history passed, traverse backward using next_question_id linkages
        if (empty($path)) {
            $currentOption = PathwayOption::with('question')->find($optionId);
            while ($currentOption) {
                $question = $currentOption->question;
                array_unshift($path, [
                    'question' => $question ? $question->question_text : '',
                    'option' => $currentOption->option_text
                ]);

                // Find a previous option whose next_question_id points to the current question
                if ($question) {
                    $currentOption = PathwayOption::where('next_question_id', $question->id)->first();
                } else {
                    $currentOption = null;
                }
            }
        }

        return $path;
    }

    /**
     * Attempt to generate dynamic pathway result using primary/fallback AI.
     */
    /**
     * Attempt to let AI decide the best result from the database based on the selection history.
     */
    protected function decideBestAiResult($option)
    {
        $path = $this->reconstructSelectionPath($option->id);
        $allResults = PathwayResult::where('status', 1)->get();
        $aiService = app(AiService::class);

        Log::info('[Pathway Wizard] Selection path history for AI:', ['path' => $path]);

        // 1. Try Primary AI Provider
        $primaryConfig = config('services.ai.primary');
        if (!empty($primaryConfig['provider']) && !empty($primaryConfig['api_key'])) {
            Log::info('[Pathway Wizard] Trying Primary AI Provider:', ['provider' => $primaryConfig['provider']]);
            try {
                $bestId = $aiService->selectBestResultId($path, $allResults->toArray(), $primaryConfig);
                Log::info('[Pathway Wizard] Primary AI response received.', ['best_id' => $bestId]);
                if ($bestId) {
                    $matchedResult = $allResults->firstWhere('id', $bestId);
                    if ($matchedResult) {
                        return $matchedResult;
                    }
                }
            } catch (\Exception $e) {
                Log::error('[Pathway Wizard] Primary AI Provider failed with error:', ['error' => $e->getMessage()]);
            }
        } else {
            Log::info('[Pathway Wizard] Primary AI Provider is not configured or missing API key.');
        }

        // 2. Try Fallback AI Provider
        $fallbackConfig = config('services.ai.fallback');
        if (!empty($fallbackConfig['provider']) && !empty($fallbackConfig['api_key'])) {
            Log::info('[Pathway Wizard] Trying Fallback AI Provider:', ['provider' => $fallbackConfig['provider']]);
            try {
                $bestId = $aiService->selectBestResultId($path, $allResults->toArray(), $fallbackConfig);
                Log::info('[Pathway Wizard] Fallback AI response received.', ['best_id' => $bestId]);
                if ($bestId) {
                    $matchedResult = $allResults->firstWhere('id', $bestId);
                    if ($matchedResult) {
                        return $matchedResult;
                    }
                }
            } catch (\Exception $e) {
                Log::error('[Pathway Wizard] Fallback AI Provider failed with error:', ['error' => $e->getMessage()]);
            }
        } else {
            Log::info('[Pathway Wizard] Fallback AI Provider is not configured or missing API key.');
        }

        return null;
    }
}
