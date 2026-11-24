<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Format waktu berbahasa Indonesia untuk tampilan (milik Nug, boleh dipakai semua modul).
 *   Waktu::relatif($tanggal)  -> "hari ini", "kemarin", "3 hari lalu", "12 Nov 2026"
 *   Waktu::lengkap($tanggal)  -> "11 Nov 2026, 08.15"
 *   Waktu::tanggal($tanggal)  -> "11 Nov 2026"
 */
class Waktu
{
    private const BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    public static function tanggal(?CarbonInterface $waktu): string
    {
        if (! $waktu) {
            return '-';
        }

        return $waktu->format('j').' '.self::BULAN[$waktu->month - 1].' '.$waktu->format('Y');
    }

    public static function lengkap(?CarbonInterface $waktu): string
    {
        if (! $waktu) {
            return '-';
        }

        return self::tanggal($waktu).', '.$waktu->format('H.i');
    }

    /** Hari relatif terhadap hari ini; lebih dari 6 hari lalu memakai tanggal. */
    public static function relatif(?CarbonInterface $waktu): string
    {
        if (! $waktu) {
            return '-';
        }

        $selisih = (int) $waktu->copy()->startOfDay()->diffInDays(now()->startOfDay());

        return match (true) {
            $selisih <= 0 => 'hari ini',
            $selisih === 1 => 'kemarin',
            $selisih <= 6 => $selisih.' hari lalu',
            default => self::tanggal($waktu),
        };
    }
}
