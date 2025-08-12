<?php

namespace Novay\Kunci\Contracts;

/**
 * Interface KunciDriver
 *
 * Mendefinisikan kontrak untuk driver manajemen kunci. Setiap driver (misalnya, File-based, AWS KMS,
 * GCP KMS, Azure Key Vault) harus mengimplementasikan interface ini untuk menyediakan
 * fungsionalitas enkripsi dan dekripsi yang konsisten.
 *
 * @package Novay\Kunci\Contracts
 */
interface KunciDriver
{
    /**
     * Enkripsi data menggunakan kunci yang dikelola oleh driver ini.
     *
     * @param string $data Data string yang akan dienkripsi.
     * @return string Data terenkripsi. Format output (misalnya, Base64) mungkin bervariasi
     * tergantung pada implementasi driver dan layanan KMS.
     * @throws \Exception Jika terjadi kesalahan selama proses enkripsi.
     */
    public function encrypt(string $data): string;

    /**
     * Dekripsi data menggunakan kunci yang dikelola oleh driver ini.
     *
     * @param string $encryptedData Data terenkripsi yang akan didekripsi.
     * @return string|false Data asli (terdekripsi) sebagai string, atau `false` jika
     * dekripsi gagal (misalnya, data rusak atau kunci salah).
     * @throws \Exception Jika terjadi kesalahan selama proses dekripsi.
     */
    public function decrypt(string $encryptedData): string|false;
}
