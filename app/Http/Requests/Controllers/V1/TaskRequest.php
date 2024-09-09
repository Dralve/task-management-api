<?php

namespace App\Http\Requests\Controllers\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|in:low,medium,high',
            'due_date' => 'nullable|date_format:Y-m-d H:i',
            'status' => 'nullable|string|in:pending,in_progress,completed',
            'assigned_to' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'Task Title',
            'description' => 'Task Description',
            'due_date' => 'Due Date',
            'priority' => 'Priority Level',
            'status' => 'Status',
            'assigned_to' => 'Assigned User',
        ];
    }

    /**
     * Get custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The :attribute field is required.',
            'description.required' => 'The :attribute field is required.',
            'due_date.date_format' => 'The :attribute must be in the format Y-m-d H:i.',
            'priority.required' => 'The :attribute field is required.',
            'priority.in' => 'The :attribute must be one of the following values: low, medium, high.',
            'status.in' => 'The :attribute must be one of the following values: pending, in_progress, completed.',
            'assigned_to.exists' => 'The selected :attribute does not exist.',
        ];
    }

    /**
     * Perform any actions after successful validation.
     *
     * @return void
     */
    protected function passedValidation()
    {
        Log::info('Task Request Validation Successful');
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        Log::error('Task Request Validation Failed:', ['errors' => $errors]);
        throw new \Exception(json_encode($errors));
    }

}
