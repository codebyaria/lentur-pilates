<?php

namespace App\Http\Requests;

use App\Actions\CompleteBooking;
use App\Models\Member;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'class_session_id' => ['required', 'integer', Rule::exists('class_sessions', 'id')],
            'package' => ['required', 'string', 'max:60'],
            'nickname' => ['required', 'string', 'max:40'],
            'whatsapp' => [
                'required',
                'string',
                'max:24',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (! preg_match('/^08\d{7,12}$/', Member::normalizeWhatsapp((string) $value))) {
                        $fail(__('Nomor WhatsApp-nya kurang pas. Contoh: 0812 3456 7890.'));
                    }
                },
            ],
            'is_new_to_pilates' => ['required', 'boolean'],
            'injury_notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Pesan ditulis dengan nada Lentur; versi Inggris di lang/en.json.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'class_session_id.required' => __('Pilih jadwal dulu, ya.'),
            'class_session_id.exists' => __('Jadwal ini tidak ditemukan. Pilih yang lain, ya.'),
            'package.required' => __('Pilih paket dulu, ya.'),
            'nickname.required' => __('Boleh tahu nama panggilanmu?'),
            'nickname.max' => __('Nama panggilannya kepanjangan. Yang singkat saja.'),
            'whatsapp.required' => __('Nomor WhatsApp dibutuhkan untuk mengabari jadwal.'),
            'is_new_to_pilates.required' => __('Pertama kali pilates atau sudah pernah?'),
            'injury_notes.max' => __('Catatannya maksimal 500 karakter.'),
        ];
    }

    public function usesExistingPackage(): bool
    {
        return $this->string('package')->toString() === CompleteBooking::USE_EXISTING_PACKAGE;
    }
}
