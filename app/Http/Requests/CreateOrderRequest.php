<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('pemesan_nama') && !$this->filled('recipient_name')) {
            $this->merge(['recipient_name' => $this->pemesan_nama]);
        }
        if ($this->filled('pemesan_hp') && !$this->filled('recipient_phone')) {
            $this->merge(['recipient_phone' => $this->pemesan_hp]);
        }
        if ($this->filled('pemesan_nama') || $this->filled('pemesan_hp')) {
            $this->merge(['is_for_other' => true]);
        }
    }

    public function rules(): array
    {
        return [
            'keluhan' => 'required|string|max:500',
            'kategori' => 'nullable|string|max:100',
            'kategori_servis' => 'nullable|string|max:100',
            'alamat_servis' => 'required|string',
            'is_for_other' => 'nullable|boolean',
            'recipient_name' => 'required_if:is_for_other,true,1|nullable|string|max:255',
            'recipient_phone' => 'required_if:is_for_other,true,1|nullable|string|max:20',
            'pemesan_nama' => 'nullable|string|max:255',
            'pemesan_hp' => 'nullable|string|max:20',
            'recipient_address' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'formatted_address' => 'nullable|string',
            'is_premium' => 'nullable|boolean',
            'search_radius' => 'nullable|numeric',
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
