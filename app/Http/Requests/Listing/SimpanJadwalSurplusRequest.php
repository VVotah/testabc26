<?php

namespace App\Http\Requests\Listing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanJadwalSurplusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hari' => [
                'required',
                'string',
                Rule::in([
                    'Senin',
                    'Selasa',
                    'Rabu',
                    'Kamis',
                    'Jumat',
                    'Sabtu',
                    'Minggu',
                ]),
            ],

            'jam' => [
                'required',
                'date_format:H:i',
            ],

            'estimasi_porsi' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':Attribute wajib diisi.',
            'hari.in' => 'Pilih hari yang valid.',
            'jam.date_format' => 'Format jam tidak valid.',
            'estimasi_porsi.integer' => 'Estimasi porsi harus berupa bilangan bulat.',
            'estimasi_porsi.min' => 'Estimasi porsi minimal 1.',
        ];
    }

    public function attributes(): array
    {
        return [
            'hari' => 'hari surplus',
            'jam' => 'jam surplus',
            'estimasi_porsi' => 'estimasi porsi',
        ];
    }
}
