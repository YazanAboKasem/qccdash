<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Services\SurveyService;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function __construct(
        private SurveyService $surveyService
    ) {}

    public function show(Survey $survey)
    {
        $survey->load(['campaign', 'questions.answerOptions']);
        return view('surveys.show', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        $data = $request->validate([
            'title.en' => 'required|string|max:255',
            'title.ar' => 'required|string|max:255',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'settings.show_progress' => 'required|boolean',
            'settings.allow_back' => 'required|boolean',
            'settings.require_all' => 'required|boolean',
            'status' => 'required|string|in:active,draft,archived',
        ]);

        $this->surveyService->update($survey, $data);

        return redirect()->route('surveys.show', $survey)->with('success', 'Survey updated successfully!');
    }

    public function duplicate(Survey $survey)
    {
        $clone = $this->surveyService->duplicate($survey->id);
        return redirect()->route('surveys.show', $clone)->with('success', 'Survey duplicated successfully!');
    }
}
