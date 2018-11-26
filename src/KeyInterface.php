<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl;

use Skriptfabrik\Openssl\Exception\OpensslErrorException;
use SplFileInfo;

/**
 * Key interface.
 *
 * @package Skriptfabrik\Openssl
 */
interface KeyInterface
{
    /**
     * RSA key type.
     */
    public const TYPE_RSA = 'RSA';

    /**
     * DSA key type.
     */
    public const TYPE_DSA = 'DSA';

    /**
     * Unsupported key type.
     */
    public const TYPE_UNSUPPORTED = 'UNSUPPORTED';

    /**
     * Supported key types.
     */
    public const SUPPORTED_TYPES = [
        OPENSSL_KEYTYPE_RSA => self::TYPE_RSA,
        OPENSSL_KEYTYPE_DSA => self::TYPE_DSA,
    ];

    /**
     * Get key type.
     *
     * @return string
     *
     * @throws OpensslErrorException
     */
    public function getType(): string;

    /**
     * Get number of bits.
     *
     * @return int
     *
     * @throws OpensslErrorException
     */
    public function getBits(): int;

    /**
     * Export key as string.
     *
     * @return string
     *
     * @throws OpensslErrorException
     */
    public function export(): string;

    /**
     * Export key to file.
     *
     * @param \SplFileInfo $file
     *
     * @return void
     *
     * @throws OpensslErrorException
     */
    public function exportToFile(SplFileInfo $file): void;
}
