<?php

namespace App\Repositories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Builder;

class QuestionRepository extends BaseRepository
{
    protected function model(): string
    {
        return Question::class;
    }

    public function getBySurvey(int $surveyId)
    {
        return $this->query()
            ->where('survey_id', $surveyId)
            ->with('answerOptions')
            ->orderBy('sort_order')
            ->get();
    }

    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Question::where('id', $id)->update(['sort_order' => $index + 1]);
        }
    }

    public function toggleActive(int $id): Question
    {
        $question = $this->findOrFail($id);
        $question->update(['is_active' => !$question->is_active]);
        return $question->fresh();
    }
}
