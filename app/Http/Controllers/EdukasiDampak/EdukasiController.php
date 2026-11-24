<?php

namespace App\Http\Controllers\EdukasiDampak;

use App\Http\Controllers\Controller;
use App\Models\Artikel;

class EdukasiController extends Controller
{
    public function index()
    {
        $artikel = Artikel::terbit()->orderByDesc('waktu_terbit')->get();

        return view('edukasi_dampak.edukasi.index', compact('artikel'));
    }

    public function show(Artikel $artikel)
    {
        abort_unless($artikel->status_artikel === Artikel::TERBIT, 404);

        $artikel->load('penulis');

        return view('edukasi_dampak.edukasi.show', compact('artikel'));
    }
}