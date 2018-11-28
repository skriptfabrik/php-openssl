<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl;

use Skriptfabrik\Openssl\Exception\OpensslErrorException;
use SplFileInfo;
use SplFileObject;

/**
 * Private key class.
 *
 * @package Skriptfabrik\Openssl
 */
class PrivateKey extends AbstractKey
{
    /**
     * @var resource
     */
    private $key;

    /**
     * @var string|null
     */
    private $passphrase;

    /**
     * Private key constructor.
     *
     * @param resource $key
     * @param string|null $passphrase
     */
    public function __construct($key, ?string $passphrase = null)
    {
        $this->key = $key;
        $this->passphrase = $passphrase;
    }

    /**
     * Create private key from string.
     *
     * @param string $pem
     * @param string|null $passphrase
     *
     * @return static
     *
     * @throws OpensslErrorException
     */
    public static function fromString(string $pem, ?string $passphrase = null)
    {
        $key = openssl_pkey_get_private($pem, (string)$passphrase);

        if ($key === false) {
            throw new OpensslErrorException(openssl_error_string());
        }

        return new static($key, $passphrase);
    }

    /**
     * Create private key from file.
     *
     * @param SplFileObject $file
     * @param string|null $passphrase
     *
     * @return static
     *
     * @throws OpensslErrorException
     */
    public static function fromFile(SplFileObject $file, ?string $passphrase = null)
    {
        $key = openssl_pkey_get_private('file://' . $file->getPathname(), (string)$passphrase);

        if ($key === false) {
            throw new OpensslErrorException(openssl_error_string());
        }

        return new static($key, $passphrase);
    }

    /**
     * Get public key.
     *
     * @return PublicKey
     *
     * @throws OpensslErrorException
     */
    public function getPublicKey(): PublicKey
    {
        $details = openssl_pkey_get_details($this->key);

        if ($details === false) {
            throw new OpensslErrorException(openssl_error_string());
        }

        return PublicKey::fromString($details['key']);
    }

    /**
     * Export private key as string.
     *
     * @return string
     *
     * @throws OpensslErrorException
     */
    public function export(): string
    {
        if (!openssl_pkey_export($this->key, $key, $this->passphrase)) {
            throw new OpensslErrorException(openssl_error_string());
        }

        return $key;
    }

    /**
     * Export private key to file.
     *
     * @param SplFileInfo $file
     *
     * @return void
     *
     * @throws OpensslErrorException
     */
    public function exportToFile(SplFileInfo $file): void
    {
        if (!openssl_pkey_export_to_file($this->key, $file->getPathname(), $this->passphrase)) {
            throw new OpensslErrorException(openssl_error_string());
        }
    }

    /**
     * Return whether the private key is encrypted.
     *
     * @return bool
     */
    public function isEncrypted(): bool
    {
        return $this->passphrase !== null;
    }

    /**
     * Get key resource.
     *
     * @return resource
     */
    protected function getKey()
    {
        return $this->key;
    }
}
