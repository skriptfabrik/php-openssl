<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Console;

use Symfony\Component\Console\Application as SymfonyApplication;

/**
 * Application class.
 *
 * @package Skriptfabrik\Openssl\Console
 */
class Application extends SymfonyApplication
{

    /**
     * The name.
     */
    public const NAME = 'OpenSSL console';

    /**
     * The version.
     */
    public const VERSION = '@package_version@';

    /**
     * Application constructor.
     */
    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);
    }
}
