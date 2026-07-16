<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\AnswerOption;
use App\Models\Question;
use App\Models\Survey;
use App\Repositories\QuestionRepository;
use App\Repositories\SurveyRepository;
use Illuminate\Support\Facades\DB;

class SurveyService
{
    public function __construct(
        private SurveyRepository $surveyRepository,
        private QuestionRepository $questionRepository,
    ) {}

    public function find(int $id): Survey
    {
        return $this->surveyRepository->getWithQuestions($id);
    }

    public function create(array $data): Survey
    {
        $survey = $this->surveyRepository->create($data);
        ActivityLog::log('created', $survey);
        return $survey;
    }

    public function update(Survey $survey, array $data): Survey
    {
        $survey = $this->surveyRepository->update($survey, $data);
        $survey->incrementVersion();
        ActivityLog::log('updated', $survey);
        return $survey;
    }

    public function delete(Survey $survey): void
    {
        ActivityLog::log('deleted', $survey, ['title' => $survey->title]);
        $this->surveyRepository->delete($survey);
    }

    public function addQuestion(Survey $survey, array $data): Question
    {
        $this->ensureOneCorrectOption($data['type'], $data['options'] ?? []);
        $data['survey_id'] = $survey->id;
        $question = $this->questionRepository->create($data);

        if (!empty($data['options'])) {
            foreach ($data['options'] as $index => $optionData) {
                $optionData['question_id'] = $question->id;
                $optionData['sort_order'] = $index + 1;
                $optionData['is_correct'] = filter_var($optionData['is_correct'] ?? false, FILTER_VALIDATE_BOOLEAN);
                AnswerOption::create($optionData);
            }
        }

        $survey->incrementVersion();
        ActivityLog::log('question_added', $survey, ['question_id' => $question->id]);

        return $question->load('answerOptions');
    }

    public function updateQuestion(Question $question, array $data): Question
    {
        if (isset($data['options'])) {
            $this->ensureOneCorrectOption($data['type'] ?? $question->type, $data['options']);
        }
        $question->update($data);

        if (isset($data['options'])) {
            // Sync options - delete removed, update existing, create new
            $existingIds = [];

            foreach ($data['options'] as $index => $optionData) {
                $optionData['is_correct'] = filter_var($optionData['is_correct'] ?? false, FILTER_VALIDATE_BOOLEAN);
                if (!empty($optionData['id'])) {
                    $option = AnswerOption::find($optionData['id']);
                    if ($option) {
                        $option->update(array_merge($optionData, ['sort_order' => $index + 1]));
                        $existingIds[] = $option->id;
                    }
                } else {
                    $optionData['question_id'] = $question->id;
                    $optionData['sort_order'] = $index + 1;
                    $option = AnswerOption::create($optionData);
                    $existingIds[] = $option->id;
                }
            }

            // Remove options not in the update
            $question->answerOptions()
                ->whereNotIn('id', $existingIds)
                ->delete();
        }

        $question->survey->incrementVersion();
        return $question->fresh()->load('answerOptions');
    }

    public function deleteQuestion(Question $question): void
    {
        $survey = $question->survey;
        ActivityLog::log('question_deleted', $survey, ['question_text' => $question->text]);
        $question->delete();
        $survey->incrementVersion();
    }

    public function reorderQuestions(array $orderedIds): void
    {
        $this->questionRepository->reorder($orderedIds);
    }

    public function toggleQuestion(int $questionId): Question
    {
        $question = $this->questionRepository->toggleActive($questionId);
        $question->survey->incrementVersion();
        return $question;
    }

    public function getActiveSurveyForDevice(int $campaignId): ?array
    {
        $survey = $this->surveyRepository->getActiveForCampaign($campaignId);

        if (!$survey) return null;

        return [
            'id' => $survey->id,
            'uuid' => $survey->uuid,
            'title' => $survey->title,
            'description' => $survey->description,
            'version' => $survey->version,
            'settings' => $survey->settings,
            'campaign' => [
                'uuid' => $survey->campaign->uuid,
                'title' => $survey->campaign->title,
                'logo_url' => $survey->campaign->logo_url,
                'settings' => $survey->campaign->settings,
            ],
            'questions' => $survey->activeQuestions->map(function ($q) {
                return [
                    'uuid' => $q->uuid,
                    'type' => $q->type,
                    'text' => $q->text,
                    'description' => $q->description,
                    'is_required' => $q->is_required,
                    'settings' => $q->settings,
                    'options' => $q->activeOptions->map(function ($o) {
                        return [
                            'uuid' => $o->uuid,
                            'label' => $o->label,
                            'value' => $o->value,
                            'icon' => $o->icon,
                            'color' => $o->color,
                            'is_correct' => $o->is_correct,
                        ];
                    })->values(),
                ];
            })->values(),
        ];
    }

    public function duplicate(int $surveyId): Survey
    {
        $survey = $this->surveyRepository->findOrFail($surveyId);
        $clone = $survey->duplicate();
        ActivityLog::log('duplicated', $clone, ['source_id' => $survey->id]);
        return $clone;
    }

    /** A quiz question must have exactly one correct option. */
    private function ensureOneCorrectOption(string $type, array $options): void
    {
        if ($type === 'text') {
            return;
        }

        $correctCount = collect($options)->filter(
            fn (array $option) => filter_var($option['is_correct'] ?? false, FILTER_VALIDATE_BOOLEAN)
        )->count();

        if ($correctCount !== 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'options' => 'Select exactly one correct answer for each quiz question.',
            ]);
        }
    }
}
