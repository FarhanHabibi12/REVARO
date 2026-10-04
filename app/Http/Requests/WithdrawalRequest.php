<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $rawAmount = (string) $this->amount;
            $cleanAmount = preg_replace('/[^\d]/', '', $rawAmount);

            $this->merge([
                'amount' => $cleanAmount !== '' ? (int) $cleanAmount : 0,
            ]);
        }

        if ($this->has('account_name') && !$this->has('account_holder_name')) {
            $this->merge([
                'account_holder_name' => $this->account_name,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:255',
            'amount' => 'required|integer|min:10000',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Nominal penarikan dana minimal adalah Rp 10.000.',
            'bank_name.required' => 'Nama bank tujuan penarikan wajib diisi.',
            'account_number.required' => 'Nomor rekening tujuan wajib diisi.',
            'account_holder_name.required' => 'Nama pemilik rekening wajib diisi.',
        ];
    }
}
