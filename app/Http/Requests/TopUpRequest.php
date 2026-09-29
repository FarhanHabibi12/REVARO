<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TopUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare input for validation (Sanitization).
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $rawAmount = (string) $this->amount;
            // Remove "Rp", dots, commas, spaces, and non-digit characters
            $cleanAmount = preg_replace('/[^\d]/', '', $rawAmount);

            $this->merge([
                'amount' => $cleanAmount !== '' ? (int) $cleanAmount : 0,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|integer|min:10000',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Nominal top up minimal adalah Rp 10.000.',
            'amount.integer' => 'Nominal top up harus berupa angka murni.',
        ];
    }
}
