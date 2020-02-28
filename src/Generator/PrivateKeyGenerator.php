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
     * RC2 128 cipher.
     */
    public const CIPHER_RC2_128 = 'rc2-128';

    /**
     * AES 128 CBC cipher.
     */
    public const CIPHER_AES_128_CBC = 'aes-128-cbc';

    /**
     * AES 192 CBC cipher.
     */
    public const CIPHER_AES_192_CBC = 'aes-192-cbc';

    /**
     * AES 256 CBC cipher.
     */
    public const CIPHER_AES_256_CBC = 'aes-256-cbc';

    /**
     * Supported ciphers.
     */
    public const SUPPORTED_CIPHERS = [
        OPENSSL_CIPHER_RC2_128 => self::CIPHER_RC2_128,
        OPENSSL_CIPHER_AES_128_CBC => self::CIPHER_AES_128_CBC,
        OPENSSL_CIPHER_AES_192_CBC => self::CIPHER_AES_192_CBC,
        OPENSSL_CIPHER_AES_256_CBC => self::CIPHER_AES_256_CBC,
    ];

    /**
     * @var string[]|null
     */
    private static $supportedDigests;

    /**
     * @var string
     */
    private $digest = 'sha256';

    /**
     * @var int
     */
    private $type = OPENSSL_KEYTYPE_RSA;

    /**
     * @var int
     */
    private $bits = 2048;

    /**
     * @var string|null
     */
    private $passphrase;

    /**
     * @var int|null
     */
    private $cipher;

    /**
     * @return string[]
     */
    public function getSupportedDigests(): array
    {
        if (self::$supportedDigests === null) {
            self::$supportedDigests = openssl_get_md_methods(true);
        }

        return self::$supportedDigests;
    }

    /**
     * Get digest.
     *
     * @return string
     */
    public function getDigest(): string
    {
        return $this->digest;
    }

    /**
     * Set digest.
     *
     * @param string $digest
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setDigest(string $digest): self
    {
        if (!in_array($digest, $this->getSupportedDigests(), true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid digest (%s), valid digests: %s',
                    $digest,
                    implode(', ', $this->getSupportedDigests())
                )
            );
        }

        $this->digest = $digest;

        return $this;
    }

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
     * Get passphrase.
     *
     * @return string|null
     */
    public function getPassphrase(): ?string
    {
        return $this->passphrase;
    }

    /**
     * Set passphrase.
     *
     * @param string|null $passphrase
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setPassphrase(?string $passphrase): self
    {
        if ($passphrase === '') {
            throw new InvalidArgumentException('Invalid passphrase, must not be empty string');
        }

        $this->passphrase = $passphrase;

        return $this;
    }

    /**
     * Get cipher.
     *
     * @return string|null
     */
    public function getCipher(): ?string
    {
        return self::SUPPORTED_CIPHERS[$this->cipher] ?? null;
    }

    /**
     * Set cipher.
     *
     * @param string|null $cipher
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setCipher(?string $cipher): self
    {
        if ($cipher === null) {
            $this->cipher = null;

            return $this;
        }

        $numericCipher = array_search(strtolower($cipher), self::SUPPORTED_CIPHERS, true);

        if ($numericCipher === false) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid cipher (%s), valid ciphers: %s',
                    $cipher,
                    implode(', ', self::SUPPORTED_CIPHERS)
                )
            );
        }

        $this->cipher = $numericCipher;

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
                'digest_alg' => $this->digest,
                'private_key_type' => $this->type,
                'private_key_bits' => $this->bits,
                'encrypt_key' => $this->passphrase !== null,
                'encrypt_key_cipher' => $this->cipher,
            ]
        );

        if ($key === false) {
            throw new OpensslErrorException(openssl_error_string());
        }

        return new PrivateKey($key, $this->passphrase);
    }
}
