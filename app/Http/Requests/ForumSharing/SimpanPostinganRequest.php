<?php

namespace App\Http\Requests\ForumSharing;

use Illuminate\Foundation\Http\FormRequest;

class SimpanPostinganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // kepemilikan dicek di controller
    }

    public function rules(): array
    {
        return [
            // Wajib saat buat (POST); opsional saat ubah (PUT), foto lama dipertahankan.
            'foto_makanan' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'file', 'mimes:jpg,jpeg,png', 'max:5120',
            ],
            'nama_makanan' => ['required', 'string', 'max:150'],
            'jumlah_porsi' => ['required', 'integer', 'min:1'],
            'lokasi_pengambilan' => ['required', 'string', 'max:255'],
            'batas_waktu' => ['required', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Kolom ini wajib diisi.',
            'foto_makanan.mimes' => 'Format foto harus JPG atau PNG.',
            'foto_makanan.max' => 'Ukuran foto maksimal 5 MB.',
            'foto_makanan.uploaded' => 'Foto gagal diunggah. Pastikan ukuran maksimal 5 MB.',
            'foto_makanan.file' => 'Berkas foto tidak valid.',
            'jumlah_porsi.integer' => 'Jumlah porsi harus bilangan bulat lebih dari 0.',
            'jumlah_porsi.min' => 'Jumlah porsi harus bilangan bulat lebih dari 0.',
            'batas_waktu.date' => 'Format batas waktu tidak valid.',
            'batas_waktu.after' => 'Batas waktu harus setelah waktu sekarang.',
            'nama_makanan.max' => 'Nama makanan maksimal 150 karakter.',
            'lokasi_pengambilan.max' => 'Lokasi pengambilan maksimal 255 karakter.',
        ];
    }
}
