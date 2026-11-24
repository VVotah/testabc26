<?php

namespace App\Http\Requests\AkunMitra;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi tambah dan ubah titik pengambilan (use case 4.1.3, FR-03).
 * Wajib: nama titik, alamat, latitude, longitude. Patokan opsional.
 */
class SimpanTitikRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses sudah dijaga route (auth + peran:Mitra/Donatur) dan controller (kepemilikan)
    }

    /** Terima koma desimal ("-6,8915") dan rapikan spasi sebelum divalidasi. */
    protected function prepareForValidation(): void
    {
        $rapikan = fn ($nilai) => is_string($nilai) ? str_replace(',', '.', trim($nilai)) : $nilai;

        $this->merge([
            'nama_titik' => is_string($this->nama_titik) ? trim($this->nama_titik) : $this->nama_titik,
            'latitude' => $rapikan($this->latitude),
            'longitude' => $rapikan($this->longitude),
        ]);
    }

    public function rules(): array
    {
        $idMitra = $this->user()->mitra?->id_mitra;
        $titik = $this->route('titik');

        return [
            'nama_titik' => [
                'required', 'string', 'max:100',
                Rule::unique('titik_pengambilan', 'nama_titik')
                    ->where('id_mitra', $idMitra)
                    ->ignore($titik?->id_titik, 'id_titik'),
            ],
            'alamat' => ['required', 'string', 'max:255'],
            'patokan' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_titik.required' => 'Nama titik wajib diisi.',
            'nama_titik.unique' => 'Kamu sudah punya titik dengan nama ini.',
            'nama_titik.max' => 'Nama titik maksimal 100 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.max' => 'Alamat maksimal 255 karakter.',
            'patokan.max' => 'Patokan maksimal 255 karakter.',
            'latitude.required' => 'Latitude wajib diisi.',
            'latitude.numeric' => 'Latitude harus berupa angka, mis. -6.8915.',
            'latitude.between' => 'Latitude harus di antara -90 dan 90.',
            'longitude.required' => 'Longitude wajib diisi.',
            'longitude.numeric' => 'Longitude harus berupa angka, mis. 107.6107.',
            'longitude.between' => 'Longitude harus di antara -180 dan 180.',
        ];
    }
}
