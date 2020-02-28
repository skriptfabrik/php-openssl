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
 * Cipher option trait test.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 *
 * @covers  \Skriptfabrik\Openssl\Console\Input\CipherOptionTrait
 */
class CipherOptionTraitTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testGettingCipherOptions(): void
    {
        $cipherOptionTrait = $this->getObjectForTrait(CipherOptionTrait::class);

        $method = new ReflectionMethod(get_class($cipherOptionTrait), 'getCipherOption');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getOption('cipher')->willReturn('aes-256-cbc');

        $this->assertSame('aes-256-cbc', $method->invoke($cipherOptionTrait, $inputProphecy->reveal()));
    }

    /**
     * @expectedException \Symfony\Component\Console\Exception\InvalidOptionException
     *
     * @expectedExceptionMessage The "--cipher" option must be string
     *
     * @throws \ReflectionException
     */
    public function testGettingCipherOptionsThrowsException(): void
    {
        $cipherOptionTrait = $this->getObjectForTrait(CipherOptionTrait::class);

        $method = new ReflectionMethod(get_class($cipherOptionTrait), 'getCipherOption');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getOption('cipher')->willReturn(['aes-256-cbc']);

        $method->invoke($cipherOptionTrait, $inputProphecy->reveal());
    }
}
