<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use thiagoalessio\TesseractOCR\TesseractOCR;

class KtpOcrService
{
    /**
     * Ekstrak Nomor Induk Kependudukan (NIK) 16 digit dari foto KTP.
     *
     * @param string $imagePath Path lengkap ke file gambar KTP
     * @return string|null NIK 16 digit atau null jika tidak terdeteksi
     */
    public static function extractNik(string $imagePath): ?string
    {
        if (!file_exists($imagePath)) {
            Log::warning('KtpOcr: file tidak ada', ['path' => $imagePath]);
            return null;
        }

        try {
            // Path Tesseract: pakai config dulu (tanpa escape .env), normalisasi untuk Windows
            $exe = config('services.tesseract.path') ?: env('TESSERACT_PATH');
            $exePath = $exe ? trim(trim($exe), '"\'') : '';
            $exePath = $exePath ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $exePath) : '';
            // Cek path absolut ke file; realpath() memastikan path valid dan file ada
            if ($exePath && is_file($exePath)) {
                $resolved = realpath($exePath);
                if ($resolved !== false) {
                    $exePath = $resolved;
                }
            }
            $ocr = new TesseractOCR($imagePath);
            if ($exePath && is_file($exePath)) {
                $ocr->executable($exePath);
            } else {
                // Fallback: tambah folder Tesseract ke PATH (mis. saat realpath gagal di Apache)
                if ($exePath && is_dir(dirname($exePath))) {
                    putenv('PATH=' . getenv('PATH') . PATH_SEPARATOR . dirname($exePath));
                }
            }
            $ocr->lang('eng');
            $text = $ocr->run();
        } catch (\Throwable $e) {
            Log::warning('KtpOcr: gagal baca gambar', [
                'path' => $imagePath,
                'error' => $e->getMessage(),
            ]);
            return null;
        }

        $nik = self::parseNikFromText($text ?? '');
        if (!$nik && !empty(trim($text ?? ''))) {
            Log::debug('KtpOcr: NIK tidak ketemu di teks', ['teks_potong' => mb_substr($text, 0, 300)]);
        }
        return $nik;
    }

    /**
     * Cari NIK 16 digit dari teks hasil OCR.
     * OCR sering memisahkan angka pakai spasi/baris (mis. "3271 0630 1002 0008") — kita satukan dulu.
     */
    public static function parseNikFromText(string $text): ?string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($text));

        // 1) Langsung dapat 16 digit berurutan
        if (preg_match('/NIK\s*:?\s*(\d{16})\b/i', $normalized, $m)) {
            return $m[1];
        }
        if (preg_match('/\b(\d{16})\b/', $normalized, $m)) {
            return $m[1];
        }

        // 2) Gabungkan semua digit di teks (OCR suka pisah "3271 0630 1002 0008")
        $digitsOnly = preg_replace('/\D/', '', $text);
        if (preg_match('/(\d{16})/', $digitsOnly, $m)) {
            return $m[1];
        }

        return null;
    }
}
