<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl;

use Skriptfabrik\Openssl\Exception\OpensslErrorException;

/**
 * Abstract key class.
 *
 * @package Skriptfabrik\Openssl
 */
abstract class AbstractKey implements KeyInterface
{
    /**
     * Get key type.
     *
     * @return string
     *
     * @throws OpensslErrorException
     */
    public function getType(): string
    {
        $details = openssl_pkey_get_details($this->getKey());

        if ($details === false) {
            throw new OpensslErrorException(openssl_error_string());
        }

        if (!isset(self::SUPPORTED_TYPES[$details['type']])) {
            return self::TYPE_UNSUPPORTED;
        }

        return self::SUPPORTED_TYPES[$details['type']];
    }

    /**
     * Get key resource.
     *
     * @return resource
     */
    abstract protected function getKey();

    /**
     * Get number of bits.
     *
     * @return int
     *
     * @throws OpensslErrorException
     */
    public function getBits(): int
    {
        $details = openssl_pkey_get_details($this->getKey());

        if ($details === false) {
            throw new OpensslErrorException(openssl_error_string());
        }

        return $details['bits'];
    }
}
