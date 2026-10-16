<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKioskVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visitor_name' => ['required', 'string', 'max:255'],
            'visitor_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{9,20}$/'],
            'visitor_institution' => ['required', 'string', 'max:255'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'purpose' => ['required', 'string'],
            'visit_type' => ['required', 'in:meet,deliver,meeting_invitation'],
            'delivery_pref' => ['nullable', 'in:hand_in,leave'],
            'photo' => ['nullable', 'string', 'max:4194304'],
            'consent' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'visitor_phone.regex' => 'Nomor HP/telepon tidak valid, mohon masukkan nomor yang valid.',
            'consent.accepted' => 'Anda harus menyetujui penggunaan data untuk melanjutkan.',
        ];
    }
}
