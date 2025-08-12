<?php 

namespace Novay\Kunci\Traits;

use Novay\Kunci\Facades\Kunci; 
use Exception;
use Illuminate\Support\Facades\Log;

trait Kunciable
{
    /**
     * Daftar atribut model yang harus dienkripsi.
     * Contoh: protected $encryptable = ['email', 'address'];
     *
     * @var array
     */
    protected $encryptable = [];

    /**
     * Override setAttribute untuk mengenkripsi data sebelum disimpan.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable) && !is_null($value)) {
            try {
                // Panggil Kunci::encrypt() tanpa parameter kunci, karena driver sudah terkonfigurasi
                $this->attributes[$key] = Kunci::encrypt((string) $value);
            } catch (Exception $e) {
                // Tangani error enkripsi, misalnya log error
                // throw new \RuntimeException("Gagal mengenkripsi atribut '{$key}': " . $e->getMessage(), 0, $e);
                Log::error("Enkripsi atribut '{$key}' gagal: " . $e->getMessage());
                $this->attributes[$key] = $value; // Simpan nilai asli jika gagal
            }
        } else {
            parent::setAttribute($key, $value);
        }
    }

    /**
     * Override getAttribute untuk mendekripsi data saat diambil.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encryptable) && !is_null($value)) {
            try {
                // Panggil Kunci::decrypt() tanpa parameter kunci, karena driver sudah terkonfigurasi
                $decryptedValue = Kunci::decrypt((string) $value);
                // Kembalikan nilai asli jika dekripsi gagal (misalnya, false)
                return $decryptedValue !== false ? $decryptedValue : $value;
            } catch (Exception $e) {
                // Tangani error dekripsi, misalnya log error
                Log::error("Dekripsi atribut '{$key}' gagal: " . $e->getMessage());
                return $value; // Kembalikan nilai terenkripsi jika gagal didekripsi
            }
        }

        return $value;
    }
}