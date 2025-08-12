<?php

namespace Novay\Kunci\Contracts;

/**
 * Interface DeterministicKunciDriver
 *
 * Interface ini menambahkan kontrak untuk driver yang mendukung
 * enkripsi dan dekripsi deterministik (searchable encryption).
 *
 * @package Novay\Kunci\Contracts
 */
interface DeterministicKunciDriver extends KunciDriver
{
    /**
     * Enkripsi data secara deterministik.
     *
     * @param string $data Data string yang akan dienkripsi secara deterministik.
     * @return string Data terenkripsi secara deterministik.
     * @throws \Exception Jika terjadi kesalahan selama proses enkripsi.
     */
    public function encryptDeterministic(string $data): string;

    /**
     * Dekripsi data yang dienkripsi secara deterministik.
     *
     * @param string $encryptedData Data terenkripsi secara deterministik.
     * @return string|false Data asli yang sudah didekripsi atau false jika gagal.
     * @throws \Exception Jika terjadi kesalahan selama proses dekripsi.
     */
    public function decryptDeterministic(string $encryptedData): string|false;
}
