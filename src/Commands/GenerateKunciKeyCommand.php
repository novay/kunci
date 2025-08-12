<?php

namespace Novay\Kunci\Commands;

use Exception;
use Illuminate\Console\Command;
use Novay\Kunci\Kunci; 

/**
 * Class GenerateKunciKeyCommand
 *
 * Perintah Artisan ini bertanggung jawab untuk menghasilkan kunci enkripsi baru
 * yang aman secara kriptografis untuk package `novay/kunci` dan menyimpannya ke file.
 * Kunci yang dihasilkan akan memiliki panjang 64 karakter heksadesimal (256-bit) dan cocok
 * untuk algoritma enkripsi AES-256-CBC yang digunakan oleh FileDriver.
 *
 * Perintah ini juga memberikan instruksi penting tentang bagaimana melindungi
 * file kunci yang dihasilkan dengan izin sistem file yang tepat.
 *
 * @package Novay\Kunci\Commands
 */
class GenerateKunciKeyCommand extends Command
{
    /**
     * Nama dan signature dari perintah konsol.
     *
     * Ini mendefinisikan bagaimana perintah dipanggil dari terminal dan
     * opsi-opsi yang dapat diterima.
     * Contoh: `php artisan kunci:generate --path=storage/app/my_key.key`
     *
     * @var string
     */
    protected $signature = 'kunci:generate {--path= : Lokasi (path) di mana file kunci harus disimpan (default: storage/app/private/kunci.key)}';

    /**
     * Deskripsi perintah konsol.
     *
     * Deskripsi singkat yang akan ditampilkan saat menjalankan `php artisan list`.
     *
     * @var string
     */
    protected $description = 'Menghasilkan kunci enkripsi Kunci baru dan menyimpannya ke file.';

    /**
     * Eksekusi perintah konsol.
     *
     * Metode ini berisi logika utama dari perintah Artisan. Ini akan
     * menghasilkan kunci baru, menentukan jalur penyimpanan (berdasarkan opsi
     * `--path` atau default), menulis kunci ke file, dan memberikan
     * pesan keberhasilan atau kegagalan kepada pengguna, termasuk peringatan
     * penting tentang izin file.
     *
     * @return int Kode keluar status perintah (self::SUCCESS atau self::FAILURE).
     * @throws \Exception Jika terjadi kesalahan saat membuat direktori atau menulis file kunci.
     */
    public function handle(): int
    {
        $key = Kunci::generateRandomKey();
        
        $path = $this->option('path') ?: storage_path('app/private/.key');

        try {
            $directory = dirname($path);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
                $this->info("Direktori '{$directory}' dibuat.");
            }

            file_put_contents($path, $key);

            $this->info("Kunci enkripsi Kunci baru telah berhasil dibuat dan disimpan di: <info>{$path}</info>");
            $this->warn("=================================================================================");
            $this->warn("PENTING: Pastikan file kunci ini dilindungi dengan izin yang ketat (misalnya, chmod 600)");
            $this->warn("dan hanya dapat diakses oleh proses web server Anda untuk mencegah akses tidak sah.");
            $this->warn("Contoh pengaturan izin (sesuaikan user/group web server Anda, e.g., www-data, nginx):");
            $this->line("  <comment>chmod 600 {$path}</comment>");
            $this->line("  <comment>chown www-data:www-data {$path}</comment>");
            $this->warn("=================================================================================");
            
            return self::SUCCESS; 
        } catch (Exception $e) {
            $this->error("Gagal membuat atau menyimpan file kunci: " . $e->getMessage());
            return self::FAILURE; 
        }
    }
}
