<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGateVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visitor_name' => ['required', 'string', 'max:255'],
            'identity_number' => ['required', 'digits:16'],
            'visitor_institution' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:1000'],
            'person_to_meet' => ['required', 'string', 'max:255'],
            'visitor_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{9,20}$/'],
            'photo' => ['nullable', 'string', 'max:4194304'],
            'consent' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'identity_number.required' => 'NIK wajib diisi sebagai tanda pengenal.',
            'identity_number.digits' => 'NIK harus terdiri dari 16 digit angka.',
            'visitor_phone.regex' => 'Nomor HP/telepon tidak valid, mohon masukkan nomor yang valid.',
            'consent.accepted' => 'Anda harus menyetujui penggunaan data untuk melanjutkan.',
        ];
    }
}
