<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Survey;
use App\Services\SurveyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSurveyController extends Controller
{
    public function __construct(
        private SurveyService $surveyService,
    ) {}

    /**
     * List surveys, optionally filtered by campaign.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Survey::with(['campaign', 'questions'])
            ->withCount('responses');

        if ($request->has('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        $surveys = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $surveys,
        ]);
    }

    /**
     * Create a new survey.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campaign_id' => 'required|integer|exists:campaigns,id',
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.ar' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'status' => 'nullable|string|in:draft,active,paused',
            'settings' => 'nullable|array',
        ]);

        $survey = $this->surveyService->create($validated);

        return response()->json([
            'success' => true,
            'data' => $survey,
            'message' => 'Survey created successfully.',
        ], 201);
    }

    /**
     * Show a survey with its questions and answer options.
     */
    public function show(int $id): JsonResponse
    {
        $survey = $this->surveyService->find($id);

        return response()->json([
            'success' => true,
            'data' => $survey,
        ]);
    }

    /**
     * Update a survey.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $survey = Survey::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|array',
            'title.en' => 'required_with:title|string|max:255',
            'title.ar' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'status' => 'nullable|string|in:draft,active,paused',
            'settings' => 'nullable|array',
        ]);

        $survey = $this->surveyService->update($survey, $validated);

        return response()->json([
            'success' => true,
            'data' => $survey,
            'message' => 'Survey updated successfully.',
        ]);
    }

    /**
     * Delete a survey.
     */
    public function destroy(int $id): JsonResponse
    {
        $survey = Survey::findOrFail($id);
        $this->surveyService->delete($survey);

        return response()->json([
            'success' => true,
            'message' => 'Survey deleted successfully.',
        ]);
    }

    /**
     * Duplicate a survey.
     */
    public function duplicate(int $survey): JsonResponse
    {
        $clone = $this->surveyService->duplicate($survey);

        return response()->json([
            'success' => true,
            'data' => $clone->load('questions.answerOptions'),
            'message' => 'Survey duplicated successfully.',
        ], 201);
    }

    // ─── Question Management ────────────────────────────────────

    /**
     * Add a question to a survey.
     */
    public function addQuestion(Request $request, int $survey): JsonResponse
    {
        $surveyModel = Survey::findOrFail($survey);

        $validated = $request->validate([
            'type' => 'required|string|in:single_choice,multi_choice,rating,text,yes_no',
            'text' => 'required|array',
            'text.en' => 'required|string',
            'text.ar' => 'nullable|string',
            'description' => 'nullable|array',
            'is_required' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'settings' => 'nullable|array',
            'options' => 'nullable|array',
            'options.*.label' => 'required|array',
            'options.*.label.en' => 'required|string',
            'options.*.label.ar' => 'nullable|string',
            'options.*.value' => 'required|string',
            'options.*.icon' => 'nullable|string',
            'options.*.color' => 'nullable|string',
            'options.*.is_correct' => 'nullable|boolean',
        ]);

        $question = $this->surveyService->addQuestion($surveyModel, $validated);

        return response()->json([
            'success' => true,
            'data' => $question,
            'message' => 'Question added successfully.',
        ], 201);
    }

    /**
     * Update a question.
     */
    public function updateQuestion(Request $request, int $question): JsonResponse
    {
        $questionModel = Question::findOrFail($question);

        $validated = $request->validate([
            'type' => 'sometimes|string|in:single_choice,multi_choice,rating,text,yes_no',
            'text' => 'sometimes|array',
            'text.en' => 'required_with:text|string',
            'text.ar' => 'nullable|string',
            'description' => 'nullable|array',
            'is_required' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'settings' => 'nullable|array',
            'options' => 'nullable|array',
            'options.*.id' => 'nullable|integer',
            'options.*.label' => 'required|array',
            'options.*.label.en' => 'required|string',
            'options.*.label.ar' => 'nullable|string',
            'options.*.value' => 'required|string',
            'options.*.icon' => 'nullable|string',
            'options.*.color' => 'nullable|string',
            'options.*.is_correct' => 'nullable|boolean',
        ]);

        $question = $this->surveyService->updateQuestion($questionModel, $validated);

        return response()->json([
            'success' => true,
            'data' => $question,
            'message' => 'Question updated successfully.',
        ]);
    }

    /**
     * Delete a question.
     */
    public function deleteQuestion(int $question): JsonResponse
    {
        $questionModel = Question::findOrFail($question);
        $this->surveyService->deleteQuestion($questionModel);

        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully.',
        ]);
    }

    /**
     * Reorder questions within a survey.
     */
    public function reorderQuestions(Request $request, int $survey): JsonResponse
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'required|integer|exists:questions,id',
        ]);

        $this->surveyService->reorderQuestions($request->question_ids);

        return response()->json([
            'success' => true,
            'message' => 'Questions reordered successfully.',
        ]);
    }

    /**
     * Toggle question active status.
     */
    public function toggleQuestion(int $question): JsonResponse
    {
        $question = $this->surveyService->toggleQuestion($question);

        return response()->json([
            'success' => true,
            'data' => $question,
            'message' => 'Question status toggled.',
        ]);
    }
}
