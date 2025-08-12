# Security adalah Kunci 🔑

`novay/kunci` is a **core Laravel package** that provides secure data encryption and decryption functionalities using OpenSSL. It offers a flexible architecture to manage encryption keys, including a default file-based driver, and serves as the foundation for integrating with various Cloud Key Management Services (KMS) via separate packages.

---

### Table of Contents
* [Features](#features)
* [Installation](#installation)
* [Key Management](#key-management)
    * [Generating a New Key](#generating-a-new-key)
    * [Key Path Configuration](#key-path-configuration)
    * [Important: Key File Security](#important-key-file-security)
* [Cloud KMS Integration](#cloud-kms-integration)
    * [Supported KMS Drivers](#supported-kms-drivers)
* [Usage](#usage)
    * [Loading Key (Automatic Driver Selection)](#loading-key-automatic-driver-selection)
    * [Encrypting Data](#encrypting-data)
    * [Decrypting Data](#decrypting-data)
* [License](#license)

---

### Features
* **Secure Encryption & Decryption:** Utilizes the industry-standard **AES-256-CBC** algorithm via the PHP OpenSSL extension.
* **Flexible Key Management:** Supports a default file-based key driver and provides an extensible architecture for integrating with various Cloud KMS providers.
* **Automatic Key Generation:** Offers an Artisan command to generate cryptographically secure 64-character (256-bit) encryption keys for the file-based driver.
* **Seamless Laravel Integration:** Integrates smoothly with Laravel through its Service Provider and Facade, allowing easy switching between key management drivers.

---

### Installation

1.  **Add the Core Package to Your Project:**
    You can easily add `novay/kunci` to your Laravel project using Composer:

    ```bash
    composer require novay/kunci
    ```

2.  **Run Composer Update (Optional):**
    If you encounter any autoloading issues, you can try running:

    ```bash
    composer dump-autoload
    ```

3.  **Publish the Configuration File (Optional):**
    To publish the `kunci.php` configuration file to your main application's `config` directory (so you can customize key paths and other settings):

    ```bash
    php artisan vendor:publish --tag=kunci-config
    ```

---

### Key Management

`novay/kunci` comes with a **file-based key driver** as its default. For enhanced security and cloud integration, consider using dedicated Cloud KMS driver packages (see [Cloud KMS Integration](#cloud-kms-integration) below).

#### Generating a New Key
For the file-based driver, you can create a new, secure 64-character (256-bit) encryption key using a dedicated Artisan command:

```bash
php artisan kunci:generate
````

By default, this command will create a `.key` file in `storage/app/private/`.

You can also specify a custom key file path:

```bash
php artisan kunci:generate --path=./path/to/your/secret.key
```

#### Key Path Configuration

For the file-based driver, this package will look for the key file at `storage/app/private/.key` by default. You can change this path via the `config/kunci.php` configuration file (after you've published it) or through the `KUNCI_KEY_FILE` environment variable in your `.env` file:

```dotenv
# Your .env file
KUNCI_KEY_FILE=/absolute/path/to/your/custom/.key
```

#### Important: Key File Security

After generating the key file, it is **critically important** to protect it with appropriate file system permissions:

  * **Restrict Read Access:** Ensure that only the system user running your web server process (e.g., `www-data` or `nginx`) can read the file.
  * **Secure Location:** Store the key file outside any publicly accessible directory (`public_html` or `public/`). The `storage/app/` directory (or its subdirectories like `storage/app/private/`) is a recommended location.

Example commands to set permissions (adjust to your web server's user/group):

```bash 
chmod 600 storage/app/private/.key
chown www-data:www-data storage/app/private/.key # Replace www-data with the appropriate user/group
```

-----

### Cloud KMS Integration

`novay/kunci` is designed to be extensible, allowing you to use various Cloud Key Management Services (KMS) as your encryption key source. This is achieved through separate, dedicated driver packages.

#### Supported KMS Drivers

Currently, the following Cloud KMS drivers are planned (or could be developed):

  * `novay/kunci-aws`: Integration with AWS Key Management Service (KMS).
  * `novay/kunci-azure`: Integration with Azure Key Vault.
  * `novay/kunci-gcp`: Integration with Google Cloud Key Management Service (KMS).

-----

### Usage

The `Kunci` Facade provides a consistent interface regardless of the underlying key management driver configured.

#### Loading Key (Automatic Driver Selection)

When using a KMS driver, you don't explicitly "load" the key in the same way as the file-based driver. The `Kunci` Facade will automatically interact with the configured KMS service to perform cryptographic operations. You simply need to ensure your application has the correct permissions (IAM roles for AWS/GCP, Azure AD for Azure) to communicate with your KMS service.

You *do not* need to manually call `Kunci::loadKeyFromFile()` when using a KMS driver. The system handles the key's interaction with the KMS service internally.

#### Encrypting Data

Encrypting data remains simple, as the Facade abstracts the underlying key management:

```php
use Kunci; // Use the Facade

$dataToEncrypt = "This is highly sensitive information.";

try {
    $encryptedData = Kunci::encrypt($dataToEncrypt); // No key parameter needed here, it's managed by the driver
    echo "Original Data: " . $dataToEncrypt . "\n";
    echo "Encrypted Data: " . $encryptedData . "\n";
} catch (\Exception $e) {
    // Handle encryption errors (e.g., KMS connectivity issues, permissions)
    die("Encryption Error: " . $e->getMessage());
}
```

#### Decrypting Data

Decrypting data also uses the same consistent Facade interface:

```php
use Kunci;

// Assume $encryptedData is a valid string encrypted by Kunci
try {
    $decryptedData = Kunci::decrypt($encryptedData); 
    
    if ($decryptedData !== false) {
        echo "Decrypted Data: " . $decryptedData . "\n";
    } else {
        echo "Failed to decrypt data. Key might be incorrect or encrypted data is corrupt.\n";
    }
} catch (\Exception $e) {
    // Handle decryption errors
    die("Decryption Error: " . $e->getMessage());
}
```

-----

### License

`novay/kunci` is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).

```
MIT License

Copyright (c) 2025 Novianto Rahmadi

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
