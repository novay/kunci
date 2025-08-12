<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Kunci Default Key Driver
    |--------------------------------------------------------------------------
    |
    | Opsi ini menentukan driver default yang akan digunakan untuk operasi
    | enkripsi dan dekripsi. Anda dapat memilih antara driver 'file'
    | atau driver Cloud KMS yang diinstal secara terpisah (misalnya, 'aws-kms',
    | 'gcp-kms', 'azure-kv').
    |
    */
    'driver' => env('KUNCI_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Kunci Driver Configurations
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat mengkonfigurasi setiap driver yang didukung oleh Kunci.
    | Konfigurasi driver yang Anda pilih di atas akan digunakan.
    |
    */
    'drivers' => [
        
        'file' => [
            /*
            |--------------------------------------------------------------------------
            | File Driver Key Path
            |--------------------------------------------------------------------------
            |
            | Path ini menunjukkan lokasi file kunci yang akan digunakan oleh FileDriver.
            | Sangat disarankan untuk menyimpan file kunci di luar direktori public,
            | misalnya di direktori storage/app/private/.
            |
            | Anda dapat membuat file kunci ini menggunakan perintah Artisan:
            | php artisan kunci:generate
            | php artisan kunci:generate --path=./my-secret-keys/app-key.key
            |
            */
            'key_file_path' => env('KUNCI_KEY_FILE', storage_path('app/private/.key')),
        ], 

        'usb' => [
            /*
            |--------------------------------------------------------------------------
            | USB Driver Key File Path
            |--------------------------------------------------------------------------
            |
            | Path ini menunjukkan lokasi file kunci pada USB drive.
            | Penting: Path ini harus valid di lingkungan server Anda
            | saat USB drive terpasang (misalnya, di Linux: /media/usb/kunci.key)
            |
            */
            'key_file_path' => env('USB_KUNCI_KEY_FILE', '/media/your_usb_label/kunci.key'), 
        ],

        /*
        |--------------------------------------------------------------------------
        | AWS KMS Driver Configuration
        |--------------------------------------------------------------------------
        |
        | Konfigurasi untuk integrasi AWS Key Management Service (KMS).
        | Catatan: Driver AWS KMS (dan lainnya) akan disediakan oleh package terpisah
        | (misalnya, novay/kunci-aws). Anda perlu menginstal package tersebut
        | untuk menggunakan driver ini.
        |
        | Kredensial AWS akan diambil dari environment (misalnya, IAM role,
        | AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY).
        |
        */
        'aws-kms' => [
            'key_id' => env('AWS_KMS_KEY_ID'), 
            'region' => env('AWS_REGION', 'ap-southeast-1'), 
        ],
        
        /*
        |--------------------------------------------------------------------------
        | Google Cloud KMS Driver Configuration
        |--------------------------------------------------------------------------
        |
        | Konfigurasi untuk integrasi Google Cloud Key Management Service (KMS).
        |
        */
        'gcp-kms' => [
            'project_id' => env('GCP_PROJECT_ID'),
            'location' => env('GCP_KMS_LOCATION', 'asia-southeast1'), 
            'key_ring_id' => env('GCP_KMS_KEY_RING_ID'),
            'key_name' => env('GCP_KMS_KEY_NAME'), 
        ],

        /*
        |--------------------------------------------------------------------------
        | Azure Key Vault Driver Configuration
        |--------------------------------------------------------------------------
        |
        | Konfigurasi untuk integrasi Azure Key Vault.
        |
        */
        'azure-kv' => [
            'vault_uri' => env('AZURE_KEY_VAULT_URI', 'https://myvault.vault.azure.net'), 
            'key_name' => env('AZURE_KEY_VAULT_KEY_NAME'), 
        ],
    ],
];

