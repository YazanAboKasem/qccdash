<?php

namespace App\Services;

use App\Models\AnswerOption;
use App\Models\Question;
use App\Models\SurveyResponse;
use App\Repositories\SurveyResponseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncService
{
    public function __construct(
        private SurveyResponseRepository $responseRepository,
    ) {}

    /**
     * Process a single response submission with UUID deduplication.
     */
    public function submitResponse(array $data): array
    {
        // Check for duplicate UUID
        if ($this->responseRepository->existsByUuid($data['uuid'])) {
            Log::info('Duplicate response UUID detected', ['uuid' => $data['uuid']]);
            return [
                'status' => 'duplicate',
                'uuid' => $data['uuid'],
                'message' => 'Response already recorded',
            ];
        }

        try {
            $response = $this->responseRepository->createWithAnswers(
                [
                    'uuid' => $data['uuid'],
                    'survey_id' => $data['survey_id'],
                    'device_id' => $data['device_id'] ?? null,
                    'language' => $data['language'] ?? 'en',
                    'status' => 'completed',
                    'started_at' => $data['started_at'] ?? null,
                    'completed_at' => $data['completed_at'] ?? now(),
                    'synced_at' => now(),
                    'survey_version' => $data['survey_version'] ?? 1,
                ],
                $this->prepareAnswers($data['answers'] ?? [])
            );

            return [
                'status' => 'success',
                'uuid' => $response->uuid,
                'id' => $response->id,
            ];
        } catch (\Exception $e) {
            Log::error('Response submission failed', [
                'uuid' => $data['uuid'],
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'error',
                'uuid' => $data['uuid'],
                'message' => 'Failed to save response',
            ];
        }
    }

    /**
     * Process a batch of responses (offline sync).
     */
    public function batchSync(array $responses, int $deviceId): array
    {
        $results = [];

        foreach ($responses as $responseData) {
            $responseData['device_id'] = $deviceId;
            $results[] = $this->submitResponse($responseData);
        }

        // Update device last sync time
        DB::table('devices')
            ->where('id', $deviceId)
            ->update(['last_sync_at' => now()]);

        $successful = collect($results)->where('status', 'success')->count();
        $duplicates = collect($results)->where('status', 'duplicate')->count();
        $errors = collect($results)->where('status', 'error')->count();

        Log::info('Batch sync completed', [
            'device_id' => $deviceId,
            'total' => count($results),
            'successful' => $successful,
            'duplicates' => $duplicates,
            'errors' => $errors,
        ]);

        return [
            'total' => count($results),
            'successful' => $successful,
            'duplicates' => $duplicates,
            'errors' => $errors,
            'details' => $results,
        ];
    }

    private function prepareAnswers(array $answers): array
    {
        return collect($answers)->map(function ($answer) {
            $prepared = [
                'question_id' => $this->resolveQuestionId($answer),
                'text_value' => $answer['text_value'] ?? null,
                'score' => 0,
            ];

            if (!empty($answer['answer_option_uuid'])) {
                $option = AnswerOption::where('uuid', $answer['answer_option_uuid'])->first();
                if ($option) {
                    $prepared['answer_option_id'] = $option->id;
                    $prepared['score'] = $option->score;
                }
            } elseif (!empty($answer['answer_option_id'])) {
                $option = AnswerOption::find($answer['answer_option_id']);
                $prepared['answer_option_id'] = $answer['answer_option_id'];
                $prepared['score'] = $option?->score ?? 0;
            }

            return $prepared;
        })->toArray();
    }

    private function resolveQuestionId(array $answer): int
    {
        if (!empty($answer['question_id'])) {
            return $answer['question_id'];
        }

        if (!empty($answer['question_uuid'])) {
            return Question::where('uuid', $answer['question_uuid'])->value('id');
        }

        throw new \InvalidArgumentException('Question ID or UUID is required');
    }
}
