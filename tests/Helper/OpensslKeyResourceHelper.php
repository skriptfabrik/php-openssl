<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Helper;

use RuntimeException;

/**
 * Key resource helper class.
 *
 * @package Skriptfabrik\Openssl\Helper
 */
class OpensslKeyResourceHelper
{
    /**
     * Create public key resource.
     *
     * @param mixed[] $config
     *
     * @return resource
     *
     * @throws \RuntimeException
     */
    public static function createPublicKeyResource(array $config)
    {
        $resource = openssl_get_publickey(openssl_pkey_get_details(static::createPrivateKeyResource($config))['key']);
        if ($resource !== false) {
            return $resource;
        }

        throw new RuntimeException('Unable to create public key resource');
    }

    /**
     * Create private key resource.
     *
     * @param mixed[] $config
     *
     * @return resource
     *
     * @throws \RuntimeException
     */
    public static function createPrivateKeyResource(array $config)
    {
        $resource = openssl_pkey_new($config);
        if ($resource !== false) {
            return $resource;
        }

        throw new RuntimeException('Unable to create private key resource');
    }
}
