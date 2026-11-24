<?php

namespace App\Http\Controllers\EdukasiDampak;

use App\Http\Controllers\Controller;
use App\Models\Kampanye;

class DaftarKampanyeController extends Controller
{
    public function index()
    {
        Kampanye::sinkronStatus();

        $kampanye = Kampanye::belumSelesai()->orderBy('tanggal_mulai')->get();

        return view('edukasi_dampak.kampanye_pengguna.index', compact('kampanye'));
    }
}