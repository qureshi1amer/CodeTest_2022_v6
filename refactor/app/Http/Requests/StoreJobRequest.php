<?php
namespace DTApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Update this as per your authorization logic
    }

    public function rules()
    {
        $rules = [
            'from_language_id' => 'required|exists:languages,id',
            'duration' => 'nullable|numeric',
            'immediate' => ['required', Rule::in(['yes', 'no'])],
            'due_date' => 'required_if:immediate,no|nullable|date_format:m/d/Y',
            'due_time' => 'required_if:immediate,no|nullable|date_format:H:i',
            'customer_phone_type' => 'nullable|boolean',
            'customer_physical_type' => 'nullable|boolean',
            'job_for' => 'required|array',
            'job_for.*' => ['in:male,female,normal,certified,certified_in_law,certified_in_helth'],
        ];

        if ($this->input('immediate') === 'no') {
            $rules['due_date'] = 'required|date_format:m/d/Y';
            $rules['due_time'] = 'required|date_format:H:i';
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        throw new HttpResponseException(response()->api([
            'status' => 'fail',
            'message' => $errors[0],
            'errors' => $errors
        ], 422));
    }
}
