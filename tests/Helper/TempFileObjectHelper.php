<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Helper;

use RuntimeException;
use SplFileObject;

/**
 * Temp file object helper class.
 *
 * @package Skriptfabrik\Openssl\Helper
 */
class TempFileObjectHelper
{
    /**
     * Create temp file.
     *
     * @return SplFileObject
     *
     * @throws \RuntimeException
     */
    public static function createTempFile(): SplFileObject
    {
        $filename = tempnam(sys_get_temp_dir(), 'php');
        if ($filename === false) {
            throw new RuntimeException('Unable to create file with unique file name');
        }

        return new SplFileObject($filename, 'w+');
    }
}
