<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Console\Input;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symfony\Component\Console\Input\InputInterface;
use function get_class;

/**
 * No override option trait test.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 *
 * @covers  \Skriptfabrik\Openssl\Console\Input\NoOverrideOptionTrait
 */
class NoOverrideOptionTraitTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testGettingBitOptions(): void
    {
        $bitOptionsTrait = $this->getObjectForTrait(NoOverrideOptionTrait::class);

        $method = new ReflectionMethod(get_class($bitOptionsTrait), 'isNoOverrideOption');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getOption('no-override')->willReturn(true);

        $this->assertTrue($method->invoke($bitOptionsTrait, $inputProphecy->reveal()));
    }

    /**
     * @expectedException \Symfony\Component\Console\Exception\InvalidOptionException
     *
     * @expectedExceptionMessage The "--no-override" option must be boolean
     *
     * @throws \ReflectionException
     */
    public function testGettingBitOptionsThrowsException(): void
    {
        $bitOptionsTrait = $this->getObjectForTrait(NoOverrideOptionTrait::class);

        $method = new ReflectionMethod(get_class($bitOptionsTrait), 'isNoOverrideOption');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getOption('no-override')->willReturn([true]);

        $method->invoke($bitOptionsTrait, $inputProphecy->reveal());
    }
}
