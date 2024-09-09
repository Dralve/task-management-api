<?php

namespace App\Http\Requests\Controllers\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UpdateTaskRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'priority' => 'sometimes|string|in:low,medium,high',
            'due_date' => 'sometimes|nullable|date_format:Y-m-d H:i',
            'status' => 'sometimes|string|in:pending,in_progress,completed',
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
            'priority' => 'Priority Level',
            'due_date' => 'Due Date',
            'status' => 'Task Status',
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
            'title.string' => 'The :attribute must be a valid string.',
            'due_date.date_format' => 'The :attribute must be in the format Y-m-d H:i.',
            'priority.in' => 'The :attribute must be one of the following: low, medium, high.',
            'status.in' => 'The :attribute must be one of the following: pending, in_progress, completed.',
            'assigned_to.exists' => 'The selected :attribute does not exist.',
            'assigned_to' => 'Tasks can only be assigned to users, not admins or managers.',
        ];
    }

    /**
     * Perform any actions after successful validation.
     *
     * @return void
     */
    protected function passedValidation()
    {
        Log::info('Task update request validated successfully.');
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        Log::error('Task update request validation failed:', ['errors' => $errors]);

        throw new \Exception(json_encode($errors));
    }
}
