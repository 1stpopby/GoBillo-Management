<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('asset'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $asset = $this->route('asset');

        return [
            'asset_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('assets', 'asset_code')->ignore($asset->id),
                'regex:/^[A-Z0-9\-]+$/',
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:asset_categories,id',
            'location_id' => 'required|exists:locations,id',
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_date' => 'required|date|before_or_equal:today',
            'purchase_cost' => 'required|numeric|min:0|max:999999999.99',
            'depreciation_method' => 'required|in:NONE,STRAIGHT_LINE',
            'depreciation_life_months' => [
                'nullable',
                'integer',
                'min:1',
                'max:600',
                'required_if:depreciation_method,STRAIGHT_LINE',
            ],
            'status' => 'required|in:IN_STOCK,ASSIGNED,MAINTENANCE,RETIRED,LOST',
            'assignee_id' => [
                'nullable',
                'exists:users,id',
                'required_if:status,ASSIGNED',
            ],
            'department' => 'nullable|string|max:255',
            'serial_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('assets', 'serial_number')->ignore($asset->id),
            ],
            'warranty_expiry' => 'nullable|date|after:purchase_date',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:asset_tags,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'asset_code.regex' => 'The asset code may only contain uppercase letters, numbers, and hyphens.',
            'asset_code.unique' => 'This asset code is already in use.',
            'purchase_date.before_or_equal' => 'The purchase date cannot be in the future.',
            'warranty_expiry.after' => 'The warranty expiry date must be after the purchase date.',
            'depreciation_life_months.required_if' => 'Depreciation life is required when using straight line depreciation.',
            'assignee_id.required_if' => 'An assignee is required when status is set to Assigned.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // If status is not ASSIGNED, clear assignee_id and department
            if ($this->status !== 'ASSIGNED') {
                $this->merge([
                    'assignee_id' => null,
                    'department' => null,
                ]);
            }

            // If depreciation_method is NONE, clear depreciation_life_months
            if ($this->depreciation_method === 'NONE') {
                $this->merge([
                    'depreciation_life_months' => null,
                ]);
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert asset code to uppercase
        if ($this->asset_code) {
            $this->merge([
                'asset_code' => strtoupper($this->asset_code),
            ]);
        }
    }
}