<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
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
            'description' => 'required|string',
            'is_featured' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => ['required', 'string', Rule::in(Project::STATUSES)],
            'type' => ['nullable', 'string', Rule::in(Project::TYPES)],
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'live_demo_link' => 'nullable|url',
            'repository_link' => 'nullable|url',
            'tech_stack_ids' => 'array',
            'tech_stack_ids.*' => 'exists:skills,id',
        ];
    }
}
