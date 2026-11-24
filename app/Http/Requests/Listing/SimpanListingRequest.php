<?php

namespace App\Http\Requests\Listing;

use App\Models\Listing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mitra = $this->user()->mitra()->first();

        $ubah = $this->route('listing') !== null;

        return [
            'nama_makanan' => [
                'required',
                'string',
                'max:100',
            ],

            'foto' => [
                $ubah ? 'nullable' : 'required',
                'file',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],

            'jumlah_porsi' => [
                'required',
                'integer',
                $ubah ? 'min:0' : 'min:1',
                'max:100000',
            ],

            'tipe_listing' => [
                'required',
                Rule::in(Listing::TIPE),
            ],

            'harga' => [
                Rule::requiredIf(
                    fn () => $this->input('tipe_listing') === Listing::TIPE_DISKON
                ),
                'nullable',
                'numeric',
                'min:1',
                'max:100000000',
            ],

            'batas_kedaluwarsa' => [
                'required',
                'date',
                'after:now',
            ],

            'id_kategori_makanan' => [
                'required',
                Rule::exists('kategori_makanan', 'id_kategori_makanan'),
            ],

            'id_titik' => [
                'required',
                Rule::exists('titik_pengambilan', 'id_titik')
                    ->where('id_mitra', $mitra?->id_mitra),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':Attribute wajib diisi.',
            'string' => ':Attribute harus berupa teks.',
            'integer' => ':Attribute harus berupa angka bulat.',
            'numeric' => ':Attribute harus berupa angka.',
            'max.string' => ':Attribute maksimal :max karakter.',

            'nama_makanan.required' => 'Nama makanan wajib diisi.',
            'foto.required' => 'Foto makanan wajib diunggah.',
            'foto.mimes' => 'Foto harus berformat JPG atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',

            'jumlah_porsi.min' => 'Jumlah porsi minimal :min.',
            'jumlah_porsi.max' => 'Jumlah porsi maksimal :max.',

            'tipe_listing.in' => 'Pilih tipe listing: Gratis atau Diskon.',

            'harga.required' => 'Harga wajib diisi untuk tipe Diskon.',
            'harga.min' => 'Harga minimal Rp 1.',

            'batas_kedaluwarsa.date' => 'Format batas kedaluwarsa tidak valid.',
            'batas_kedaluwarsa.after' => 'Batas kedaluwarsa harus setelah waktu sekarang.',

            'id_kategori_makanan.required' => 'Pilih kategori makanan.',
            'id_kategori_makanan.exists' => 'Kategori makanan tidak valid.',

            'id_titik.required' => 'Pilih titik pengambilan.',
            'id_titik.exists' => 'Titik pengambilan tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_makanan' => 'nama makanan',
            'foto' => 'foto makanan',
            'jumlah_porsi' => 'jumlah porsi',
            'tipe_listing' => 'tipe listing',
            'harga' => 'harga',
            'batas_kedaluwarsa' => 'batas kedaluwarsa',
            'id_kategori_makanan' => 'kategori makanan',
            'id_titik' => 'titik pengambilan',
        ];
    }
}
