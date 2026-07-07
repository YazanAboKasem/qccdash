<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Survey;
use App\Services\SurveyService;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct(
        private SurveyService $surveyService
    ) {}

    public function store(Request $request, Survey $survey)
    {
        $data = $request->validate([
            'text.en' => 'required|string',
            'text.ar' => 'required|string',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'type' => 'required|string|in:single_choice,rating,yes_no,text',
            'is_required' => 'nullable',
            'settings.code' => 'nullable|string|max:50',
            'settings.section' => 'nullable|string|max:255',
            'settings.section_ar' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'options.*.label.en' => 'required_with:options|string',
            'options.*.label.ar' => 'required_with:options|string',
            'options.*.value' => 'required_with:options|string',
            'options.*.score' => 'required_with:options|integer',
            'options.*.icon' => 'nullable|string',
            'options.*.color' => 'nullable|string',
        ]);

        $data['is_required'] = $request->has('is_required');

        $this->surveyService->addQuestion($survey, $data);

        return redirect()->route('surveys.show', $survey)->with('success', 'Question added successfully!');
    }

    public function edit(Question $question)
    {
        $question->load(['survey', 'answerOptions']);
        return view('questions.edit', compact('question'));
    }

    public function update(Request $request, Question $question)
    {
        $data = $request->validate([
            'text.en' => 'required|string',
            'text.ar' => 'required|string',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'type' => 'required|string|in:single_choice,rating,yes_no,text',
            'is_required' => 'nullable',
            'settings.code' => 'nullable|string|max:50',
            'settings.section' => 'nullable|string|max:255',
            'settings.section_ar' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'options.*.id' => 'nullable|integer',
            'options.*.label.en' => 'required_with:options|string',
            'options.*.label.ar' => 'required_with:options|string',
            'options.*.value' => 'required_with:options|string',
            'options.*.score' => 'required_with:options|integer',
            'options.*.icon' => 'nullable|string',
            'options.*.color' => 'nullable|string',
        ]);

        $data['is_required'] = $request->has('is_required');

        $this->surveyService->updateQuestion($question, $data);

        return redirect()->route('surveys.show', $question->survey_id)->with('success', 'Question updated successfully!');
    }

    public function destroy(Question $question)
    {
        $surveyId = $question->survey_id;
        $this->surveyService->deleteQuestion($question);

        return redirect()->route('surveys.show', $surveyId)->with('success', 'Question deleted successfully!');
    }

    public function toggle(Question $question)
    {
        $this->surveyService->toggleQuestion($question->id);
        return back()->with('success', 'Question status toggled!');
    }

    public function reorder(Request $request, Survey $survey)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:questions,id',
        ]);

        $this->surveyService->reorderQuestions($request->order);

        return response()->json(['success' => true]);
    }
}
