<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SubmitResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuid' => 'required|uuid',
            'survey_id' => 'required|integer|exists:surveys,id',
            'language' => 'required|string|in:en,ar',
            'started_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'survey_version' => 'nullable|integer',
            'answers' => 'required|array|min:1',
            'answers.*.question_uuid' => 'required_without:answers.*.question_id|string',
            'answers.*.question_id' => 'required_without:answers.*.question_uuid|integer',
            'answers.*.answer_option_uuid' => 'nullable|string',
            'answers.*.answer_option_id' => 'nullable|integer',
            'answers.*.text_value' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'uuid.required' => 'Response UUID is required for deduplication.',
            'uuid.uuid' => 'Response UUID must be a valid UUID format.',
            'answers.required' => 'At least one answer is required.',
        ];
    }
}
