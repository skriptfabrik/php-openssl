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
     * Private key constructor.
     *
     * @param resource $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Create private key from string.
     *
     * @param string $pem
     *
     * @return static
     *
     * @throws OpensslErrorException
     */
    public static function fromString(string $pem)
    {
        $key = openssl_pkey_get_private($pem);

        if ($key === false) {
            throw new OpensslErrorException(openssl_error_string());
        }

        return new static($key);
    }

    /**
     * Create private key from file.
     *
     * @param SplFileObject $file
     *
     * @return static
     *
     * @throws OpensslErrorException
     */
    public static function fromFile(SplFileObject $file)
    {
        $key = openssl_pkey_get_private('file://' . $file->getPathname());

        if ($key === false) {
            throw new OpensslErrorException(openssl_error_string());
        }

        return new static($key);
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
        if (!openssl_pkey_export($this->key, $key)) {
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
        if (!openssl_pkey_export_to_file($this->key, $file->getPathname())) {
            throw new OpensslErrorException(openssl_error_string());
        }
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
