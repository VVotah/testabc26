<?php

namespace App\Http\Requests\ModerasiKualitas;

use App\Models\LaporanMasalah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanLaporanMasalahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $aturanIdObjek = [
            'required',
            'integer',
            'min:1',
        ];

        // Pastikan objek yang dilaporkan benar-benar ada.
        $tabelObjek = [
            LaporanMasalah::OBJEK_LISTING => ['listing', 'id_listing'],
            LaporanMasalah::OBJEK_AKUN => ['pengguna', 'id_pengguna'],
            LaporanMasalah::OBJEK_POSTINGAN_SHARING => ['postingan_sharing', 'id_postingan_sharing'],
        ][$this->input('jenis_objek')] ?? null;

        if ($tabelObjek !== null) {
            $aturanIdObjek[] = Rule::exists(...$tabelObjek);
        }

        return [
            'jenis_objek' => [
                'required',
                'string',
                Rule::in([
                    LaporanMasalah::OBJEK_LISTING,
                    LaporanMasalah::OBJEK_AKUN,
                    LaporanMasalah::OBJEK_POSTINGAN_SHARING,
                ]),
            ],

            'id_objek' => $aturanIdObjek,

            'kategori_masalah' => [
                'required',
                Rule::in([
                    'Kedaluwarsa',
                    'Tidak sesuai',
                    'Spam',
                ]),
            ],

            'deskripsi_masalah' => [
                'nullable',
                'string',
            ],

            'bukti_foto' => [
                Rule::requiredIf(
                    fn () => $this->input('jenis_objek') === LaporanMasalah::OBJEK_POSTINGAN_SHARING
                ),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_objek.required' => 'Jenis objek wajib dipilih.',
            'jenis_objek.in' => 'Jenis objek tidak valid.',
            'id_objek.required' => 'ID objek wajib diisi.',
            'id_objek.integer' => 'ID objek harus berupa angka.',
            'id_objek.exists' => ($this->input('jenis_objek') ?: 'Objek').' yang dilaporkan tidak ditemukan.',
            'kategori_masalah.required' => 'Kategori masalah wajib dipilih.',
            'kategori_masalah.in' => 'Kategori masalah tidak valid.',
            'bukti_foto.required' => 'Bukti foto wajib diunggah untuk laporan postingan sharing.',
            'bukti_foto.file' => 'Bukti foto harus berupa file.',
            'bukti_foto.mimes' => 'Bukti foto harus berformat JPG, JPEG, atau PNG.',
            'bukti_foto.max' => 'Ukuran bukti foto maksimal 5 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'jenis_objek' => 'jenis objek',
            'id_objek' => 'ID objek',
            'kategori_masalah' => 'kategori masalah',
            'deskripsi_masalah' => 'deskripsi masalah',
            'bukti_foto' => 'bukti foto',
        ];
    }
}
