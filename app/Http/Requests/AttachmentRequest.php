<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $asset = $this->route('asset');
        return $this->user()->can('attach', $asset);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files' => 'required|array|min:1|max:5',
            'files.*' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xlsx,xls,txt',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'files.required' => 'Please select at least one file to upload.',
            'files.max' => 'You can upload a maximum of 5 files at once.',
            'files.*.required' => 'Each file is required.',
            'files.*.file' => 'Each upload must be a valid file.',
            'files.*.max' => 'Each file must not exceed 10MB.',
            'files.*.mimes' => 'Files must be: jpg, jpeg, png, webp, pdf, doc, docx, xlsx, xls, or txt.',
        ];
    }
}