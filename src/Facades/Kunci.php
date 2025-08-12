<?php

namespace Novay\Kunci\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Kunci
 *
 * Ketika Anda memanggil `Kunci::encrypt(...)` atau `Kunci::decrypt(...)`,
 * Facade ini akan secara otomatis me-resolve instance dari kelas `\Novay\Kunci\Kunci`
 * dari container dan mendelegasikan panggilan metode ke driver enkripsi yang aktif.
 *
 * @see \Novay\Kunci\Kunci
 * @package Novay\Kunci\Facades
 *
 * @method static \Novay\Kunci\Contracts\KunciDriver driver() Mendapatkan instance driver enkripsi yang aktif.
 * @method static string encrypt(string $data) Mengenkripsi data menggunakan driver aktif.
 * @method static string|false decrypt(string $encryptedData) Mendekripsi data menggunakan driver aktif.
 * @method static string generateRandomKey() Menghasilkan kunci acak 64 karakter (256-bit).
 * @method static string loadKeyFromFile(string $filePath) Memuat kunci dari file.
 */
class Kunci extends Facade
{
    /**
     * Dapatkan nama komponen terdaftar dari container Laravel.
     *
     * Metode ini memberitahu Facade nama binding di container layanan Laravel
     * yang harus di-resolve ketika Facade dipanggil. Dalam kasus ini,
     * Facade 'Kunci' akan me-resolve binding 'kunci' yang didaftarkan
     * di `KunciServiceProvider`.
     *
     * @return string Nama binding di container layanan Laravel.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'kunci'; 
    }
}
