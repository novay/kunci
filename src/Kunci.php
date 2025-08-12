<?php

namespace Novay\Kunci;

use Illuminate\Contracts\Foundation\Application;
use Novay\Kunci\Contracts\KunciDriver;
use Novay\Kunci\Drivers\FileDriver; 
use Exception;

/**
 * Class Kunci
 *
 * Kelas `Kunci` bertindak sebagai manajer driver untuk fungsionalitas enkripsi dan dekripsi.
 * Ini bertanggung jawab untuk memilih dan menginisialisasi driver yang sesuai
 * berdasarkan konfigurasi aplikasi (file-based, AWS KMS, GCP KMS, dll.)
 * dan mendelegasikan operasi enkripsi/dekripsi ke driver tersebut.
 *
 * Kelas ini juga menyediakan metode utilitas statis untuk menghasilkan dan memuat kunci
 * yang bersifat umum dan tidak terikat pada driver spesifik.
 *
 * @package Novay\Kunci
 */
class Kunci
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application Instance aplikasi Laravel.
     */
    protected Application $app;

    /**
     * @var array Cache untuk instance driver yang sudah dibuat.
     */
    protected array $drivers = [];

    /**
     * @var \Novay\Kunci\Contracts\KunciDriver|null Instance driver yang aktif saat ini.
     */
    protected ?KunciDriver $activeDriver = null;

    /**
     * Kunci constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app Instance aplikasi Laravel.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Mendapatkan instance driver enkripsi yang aktif.
     *
     * Metode ini akan membaca konfigurasi driver dari 'kunci.driver'
     * dan menginisialisasi driver yang sesuai.
     *
     * @return \Novay\Kunci\Contracts\KunciDriver Instance driver yang sedang digunakan.
     * @throws \Exception Jika driver yang dikonfigurasi tidak valid atau tidak didukung.
     */
    public function driver(): KunciDriver
    {
        if ($this->activeDriver) {
            return $this->activeDriver;
        }

        $config = $this->app['config']->get('kunci');
        $driverName = $config['driver'] ?? 'file'; // Default driver adalah 'file'

        if (!isset($this->drivers[$driverName])) {
            $this->drivers[$driverName] = $this->createDriver($driverName, $config['drivers'][$driverName] ?? []);
        }

        $this->activeDriver = $this->drivers[$driverName];
        return $this->activeDriver;
    }

    /**
     * Membuat instance driver berdasarkan nama driver dan konfigurasinya.
     *
     * @param string $driverName Nama driver yang akan dibuat (e.g., 'file', 'aws-kms').
     * @param array $config Konfigurasi spesifik untuk driver ini.
     * @return \Novay\Kunci\Contracts\KunciDriver Instance driver yang baru dibuat.
     * @throws \Exception Jika driver tidak dikenal atau tidak dapat diinisialisasi.
     */
    protected function createDriver(string $driverName, array $config): KunciDriver
    {
        switch ($driverName) {
            case 'file':
                return new FileDriver($config);
            // Case untuk driver Cloud KMS (aws-kms, gcp-kms, azure-kv) akan ditambahkan
            // di package terpisah dan didaftarkan melalui Service Provider mereka.
            // Contoh (akan ada di package kunci-aws):
            // case 'aws-kms':
            //     return $this->app->make(\Novay\KunciAws\Drivers\AwsKmsDriver::class, ['config' => $config]);
            default:
                throw new Exception("Driver Kunci '{$driverName}' tidak didukung.");
        }
    }

    /**
     * Enkripsi data menggunakan driver yang aktif saat ini.
     *
     * @param string $data Data string yang akan dienkripsi.
     * @return string Data terenkripsi.
     * @throws \Exception Jika terjadi kesalahan selama proses enkripsi.
     */
    public function encrypt(string $data): string
    {
        return $this->driver()->encrypt($data);
    }

    /**
     * Dekripsi data menggunakan driver yang aktif saat ini.
     *
     * @param string $encryptedData Data terenkripsi yang akan didekripsi.
     * @return string|false Data asli (terdekripsi) sebagai string, atau `false` jika dekripsi gagal.
     * @throws \Exception Jika terjadi kesalahan selama proses dekripsi.
     */
    public function decrypt(string $encryptedData): string|false
    {
        return $this->driver()->decrypt($encryptedData);
    }

    /**
     * Menghasilkan kunci acak yang aman secara kriptografis untuk enkripsi AES-256.
     *
     * Kunci yang dihasilkan adalah string heksadesimal sepanjang 64 karakter,
     * yang setara dengan 32 byte acak (256 bit). Kunci ini cocok digunakan sebagai
     * kunci untuk driver file-based.
     *
     * @return string Kunci acak yang aman sepanjang 64 karakter heksadesimal.
     */
    public static function generateRandomKey(): string
    {
        return bin2hex(random_bytes(32)); 
    }

    /**
     * Memuat kunci enkripsi dari file yang ditentukan.
     *
     * Metode ini akan membaca isi dari file yang diberikan path-nya.
     * Isi file akan dipangkas dari spasi putih (whitespace) dan kemudian
     * divalidasi untuk memastikan bahwa itu adalah kunci yang valid (tidak kosong
     * dan memiliki panjang 64 karakter heksadesimal).
     *
     * @param string $filePath Path lengkap ke file kunci.
     * @return string Kunci enkripsi yang dimuat dari file.
     * @throws \Exception Jika file tidak ada, tidak dapat dibaca (masalah izin),
     * atau jika kunci di dalam file tidak valid (kosong atau
     * panjangnya bukan 64 karakter heksadesimal).
     */
    public static function loadKeyFromFile(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new Exception("File kunci tidak ditemukan: " . $filePath);
        }

        if (!is_readable($filePath)) {
            throw new Exception("File kunci tidak dapat dibaca, periksa izin: " . $filePath);
        }

        $key = trim(file_get_contents($filePath)); 

        if (empty($key) || strlen($key) !== 64) {
            throw new Exception("Kunci dalam file tidak valid atau kosong. Pastikan 64 karakter heksadesimal (32 byte).");
        }

        return $key;
    }
}
