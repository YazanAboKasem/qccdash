<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class BatchSyncRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'responses' => 'required|array|min:1|max:100',
            'responses.*.uuid' => 'required|uuid',
            'responses.*.survey_id' => 'required|integer|exists:surveys,id',
            'responses.*.language' => 'required|string|in:en,ar',
            'responses.*.started_at' => 'nullable|date',
            'responses.*.completed_at' => 'nullable|date',
            'responses.*.survey_version' => 'nullable|integer',
            'responses.*.answers' => 'required|array|min:1',
            'responses.*.answers.*.question_uuid' => 'required_without:responses.*.answers.*.question_id|string',
            'responses.*.answers.*.question_id' => 'required_without:responses.*.answers.*.question_uuid|integer',
            'responses.*.answers.*.answer_option_uuid' => 'nullable|string',
            'responses.*.answers.*.answer_option_id' => 'nullable|integer',
            'responses.*.answers.*.text_value' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'responses.required' => 'At least one response is required for batch sync.',
            'responses.max' => 'Maximum 100 responses per batch sync.',
        ];
    }
}
