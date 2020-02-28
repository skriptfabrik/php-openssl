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
 * Digest option trait test.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 *
 * @covers  \Skriptfabrik\Openssl\Console\Input\DigestOptionTrait
 */
class DigestOptionTraitTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testGettingDigestOptions(): void
    {
        $digestOptionTrait = $this->getObjectForTrait(DigestOptionTrait::class);

        $method = new ReflectionMethod(get_class($digestOptionTrait), 'getDigestOption');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getOption('digest')->willReturn('sha256');

        $this->assertSame('sha256', $method->invoke($digestOptionTrait, $inputProphecy->reveal()));
    }

    /**
     * @expectedException \Symfony\Component\Console\Exception\InvalidOptionException
     *
     * @expectedExceptionMessage The "--digest" option must be string
     *
     * @throws \ReflectionException
     */
    public function testGettingDigestOptionsThrowsException(): void
    {
        $digestOptionTrait = $this->getObjectForTrait(DigestOptionTrait::class);

        $method = new ReflectionMethod(get_class($digestOptionTrait), 'getDigestOption');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getOption('digest')->willReturn(['sha256']);

        $method->invoke($digestOptionTrait, $inputProphecy->reveal());
    }
}
