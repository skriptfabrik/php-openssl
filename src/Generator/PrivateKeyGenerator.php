<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Generator;

use Skriptfabrik\Openssl\Exception\InvalidArgumentException;
use Skriptfabrik\Openssl\Exception\OpensslErrorException;
use Skriptfabrik\Openssl\PrivateKey;

/**
 * Private key generator class.
 *
 * @package Skriptfabrik\Openssl\Generator
 */
class PrivateKeyGenerator
{
    /**
     * The minimum number of bits.
     */
    public const MIN_BITS = 64;

    /**
     * @var int
     */
    private $type = OPENSSL_KEYTYPE_RSA;

    /**
     * @var int
     */
    private $bits = 2048;

    /**
     * Get type.
     *
     * @return string
     */
    public function getType(): string
    {
        return PrivateKey::SUPPORTED_TYPES[$this->type];
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setType(string $type): self
    {
        $numericType = array_search(strtoupper($type), PrivateKey::SUPPORTED_TYPES, true);

        if ($numericType === false) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid key type (%s), valid key types: %s',
                    $type,
                    implode(', ', PrivateKey::SUPPORTED_TYPES)
                )
            );
        }

        $this->type = $numericType;

        return $this;
    }

    /**
     * Get bits.
     *
     * @return int
     */
    public function getBits(): int
    {
        return $this->bits;
    }

    /**
     * Set bits.
     *
     * @param int $bits
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setBits(int $bits): self
    {
        if ($bits < self::MIN_BITS) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid number of bits (%s), must be greater than or equal %s',
                    $bits,
                    self::MIN_BITS
                )
            );
        }

        $this->bits = $bits;

        return $this;
    }

    /**
     * Generate private key.
     *
     * @return PrivateKey
     *
     * @throws OpensslErrorException
     */
    public function generate(): PrivateKey
    {
        $key = openssl_pkey_new(
            [
                'private_key_type' => $this->type,
                'private_key_bits' => $this->bits,
            ]
        );

        if ($key === false) {
            throw new OpensslErrorException(openssl_error_string());
        }

        return new PrivateKey($key);
    }
}
