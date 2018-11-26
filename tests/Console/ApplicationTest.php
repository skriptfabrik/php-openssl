<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Console;

use PHPUnit\Framework\TestCase;

/**
 * Application test class.
 *
 * @package Skriptfabrik\Openssl\Console
 *
 * @covers  \Skriptfabrik\Openssl\Console\Application
 */
class ApplicationTest extends TestCase
{
    public function testConsoleApplication(): void
    {
        $consoleApplication = new Application();

        $this->assertSame(Application::NAME, $consoleApplication->getName());
        $this->assertSame(Application::VERSION, $consoleApplication->getVersion());
    }
}
