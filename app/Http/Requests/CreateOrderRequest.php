<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keluhan' => 'required|string|max:500',
            'alamat_servis' => 'required|string',
            'is_for_other' => 'nullable|boolean',
            'recipient_name' => 'required_if:is_for_other,true,1|nullable|string|max:255',
            'recipient_phone' => 'required_if:is_for_other,true,1|nullable|string|max:20',
            'recipient_address' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'formatted_address' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'keluhan.max' => 'Deskripsi keluhan perbaikan tidak boleh melebihi 500 karakter.',
            'recipient_name.required_if' => 'Nama penerima wajib diisi jika pemesanan untuk orang lain.',
            'recipient_phone.required_if' => 'Nomor HP penerima wajib diisi jika pemesanan untuk orang lain.',
        ];
    }
}
