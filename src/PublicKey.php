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
use function is_array;

/**
 * Public key class.
 *
 * @package Skriptfabrik\Openssl
 */
class PublicKey extends AbstractKey
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
     * Create public key from string.
     *
     * @param string $pem
     *
     * @return static
     *
     * @throws OpensslErrorException
     */
    public static function fromString(string $pem)
    {
        $key = openssl_pkey_get_public($pem);

        if ($key === false) {
            throw new OpensslErrorException(openssl_error_string());
        }

        return new static($key);
    }

    /**
     * Create public key from file.
     *
     * @param \SplFileObject $file
     *
     * @return static
     *
     * @throws OpensslErrorException
     */
    public static function fromFile(SplFileObject $file)
    {
        $key = openssl_pkey_get_public('file://' . $file->getPathname());

        if ($key === false) {
            throw new OpensslErrorException(openssl_error_string());
        }

        return new static($key);
    }

    /**
     * Export public key to file.
     *
     * @param \SplFileInfo $file
     *
     * @throws OpensslErrorException
     */
    public function exportToFile(SplFileInfo $file): void
    {
        $publicKeyContent = $this->export();

        $file->openFile('w')->fwrite($publicKeyContent);
    }

    /**
     * Export public key as string.
     *
     * @return string
     *
     * @throws OpensslErrorException
     */
    public function export(): string
    {
        $details = openssl_pkey_get_details($this->key);

        if (!is_array($details)) {
            throw new OpensslErrorException(openssl_error_string());
        }

        return $details['key'];
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
