<?php

namespace Novay\Kunci;

use Illuminate\Support\ServiceProvider;
use Novay\Kunci\Commands\GenerateKunciKeyCommand;

/**
 * Class KunciServiceProvider
 *
 * KunciServiceProvider adalah Service Provider untuk package 'novay/kunci'.
 * Kelas ini bertanggung jawab untuk mendaftarkan layanan package ke container
 * Laravel, menggabungkan konfigurasi, mempublikasikan aset (seperti file
 * konfigurasi), dan mendaftarkan perintah Artisan.
 *
 * @package Novay\Kunci
 */
class KunciServiceProvider extends ServiceProvider
{
    /**
     * Daftarkan layanan package.
     *
     * Metode ini dipanggil saat service provider diregistrasi. Ini adalah tempat
     * yang tepat untuk mengikat kelas ke container layanan Laravel.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind kelas utama Kunci ke container Laravel sebagai singleton.
        $this->app->singleton('kunci', function ($app) {
            return new Kunci($app);
        });

        // Gabungkan konfigurasi package dengan konfigurasi aplikasi Laravel.
        $this->mergeConfigFrom(
            __DIR__.'/../config/kunci.php', 'kunci'
        );
    }

    /**
     * Bootstrap layanan package.
     *
     * Metode ini dipanggil setelah semua service provider lain telah didaftarkan.
     * Ini adalah tempat yang tepat untuk mempublikasikan file, mendaftarkan
     * perintah Artisan, atau melakukan inisialisasi lainnya yang bergantung
     * pada service lain.
     *
     * @return void
     */
    public function boot(): void
    {
        // Pengguna dapat menjalankan `php artisan vendor:publish --tag=kunci-config`
        $this->publishes([
            __DIR__.'/../config/kunci.php' => config_path('kunci.php'),
        ], 'kunci-config'); 

        // Ini memastikan perintah 'kunci:generate' tersedia untuk digunakan.
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateKunciKeyCommand::class,
            ]);
        }
    }
}
