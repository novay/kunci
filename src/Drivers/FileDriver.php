<?php

namespace Novay\Kunci\Drivers;

use Novay\Kunci\Contracts\DeterministicKunciDriver;
use Novay\Kunci\Kunci as KunciUtility; 
use Exception;

/**
 * Class FileDriver
 *
 * Implementasi KunciDriver untuk manajemen kunci berbasis file lokal.
 * Driver ini mengenkripsi dan mendekripsi data menggunakan kunci AES-256-CBC
 * yang dimuat dari file teks biasa.
 *
 * @package Novay\Kunci\Drivers
 */
class FileDriver implements DeterministicKunciDriver
{
    /**
     * @var string Path lengkap ke file kunci.
     */
    protected string $keyFilePath;

    /**
     * FileDriver constructor.
     *
     * @param array $config Konfigurasi driver, harus mengandung 'key_file_path'.
     * @throws \Exception Jika konfigurasi 'key_file_path' tidak ada.
     */
    public function __construct(array $config)
    {
        if (!isset($config['key_file_path'])) {
            throw new Exception("Konfigurasi 'key_file_path' tidak ditemukan untuk FileDriver.");
        }
        $this->keyFilePath = $config['key_file_path'];
    }

    /**
     * Memuat kunci enkripsi dari file yang ditentukan.
     *
     * Menggunakan helper `KunciUtility::loadKeyFromFile` untuk membaca dan memvalidasi kunci.
     *
     * @return string Kunci enkripsi yang dimuat dari file.
     * @throws \Exception Jika file kunci tidak ditemukan, tidak dapat dibaca, atau tidak valid.
     */
    protected function getKey(): string
    {
        return KunciUtility::loadKeyFromFile($this->keyFilePath);
    }

    /**
     * Enkripsi data menggunakan algoritma AES-256-CBC dengan kunci dari file.
     *
     * Data terenkripsi akan dikodekan dalam Base64 bersama dengan Initialization Vector (IV).
     *
     * @param string $data Data string yang akan dienkripsi.
     * @return string Data terenkripsi dalam format Base64 (termasuk IV).
     * @throws \Exception Jika terjadi kesalahan selama proses enkripsi.
     */
    public function encrypt(string $data): string
    {
        $key = $this->getKey();

        $ciphering = "aes-256-cbc";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $iv = openssl_random_pseudo_bytes($iv_length);
        
        $encrypted = openssl_encrypt($data, $ciphering, $key, 0, $iv);
        
        if ($encrypted === false) {
            throw new Exception('Enkripsi gagal.');
        }

        return base64_encode($encrypted . '::' . $iv);
    }

    /**
     * Dekripsi data yang sebelumnya dienkripsi oleh FileDriver.
     *
     * Akan memisahkan IV dari data terenkripsi dan mendekripsinya menggunakan kunci dari file.
     *
     * @param string $encryptedData Data terenkripsi dalam format Base64 (termasuk IV).
     * @return string|false Data asli (terdekripsi) sebagai string, atau `false` jika
     * format data terenkripsi tidak valid atau proses dekripsi gagal.
     * @throws \Exception Jika terjadi kesalahan selama proses dekripsi.
     */
    public function decrypt(string $encryptedData): string|false
    {
        $key = $this->getKey();

        $decoded = base64_decode($encryptedData);

        if ($decoded === false || strpos($decoded, '::') === false) {
            return false; 
        }

        list($encrypted, $iv) = explode('::', $decoded, 2);

        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    }

    /**
     * Enkripsi deterministik (searchable encryption).
     *
     * Menggunakan AES-256-ECB tanpa IV agar ciphertext selalu sama untuk plaintext yang sama.
     * Format data dienkode Base64 tanpa IV karena ECB tidak menggunakan IV.
     *
     * @param string $data Plaintext yang akan dienkripsi deterministik.
     * @return string Ciphertext dalam Base64.
     * @throws \Exception Jika enkripsi gagal.
     */
    public function encryptDeterministic(string $data): string
    {
        $key = $this->getKey();

        $encrypted = openssl_encrypt($data, 'aes-256-ecb', $key, OPENSSL_RAW_DATA);

        if ($encrypted === false) {
            throw new Exception('Enkripsi deterministik gagal.');
        }

        return base64_encode($encrypted);
    }

    /**
     * Dekripsi data yang dienkripsi secara deterministik.
     *
     * @param string $encryptedData Ciphertext dalam Base64.
     * @return string|false Plaintext hasil dekripsi atau false jika gagal.
     * @throws \Exception Jika dekripsi gagal.
     */
    public function decryptDeterministic(string $encryptedData): string|false
    {
        $key = $this->getKey();

        $decoded = base64_decode($encryptedData);

        if ($decoded === false) {
            return false;
        }

        return openssl_decrypt($decoded, 'aes-256-ecb', $key, OPENSSL_RAW_DATA);
    }
    
}
