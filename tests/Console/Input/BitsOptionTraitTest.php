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
 * Bits option trait test.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 *
 * @covers  \Skriptfabrik\Openssl\Console\Input\BitsOptionTrait
 */
class BitsOptionTraitTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testGettingBitsOptions(): void
    {
        $bitsOptionTrait = $this->getObjectForTrait(BitsOptionTrait::class);

        $method = new ReflectionMethod(get_class($bitsOptionTrait), 'getBitsOption');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getOption('bits')->willReturn('1024');

        $this->assertSame(1024, $method->invoke($bitsOptionTrait, $inputProphecy->reveal()));
    }

    /**
     * @expectedException \Symfony\Component\Console\Exception\InvalidOptionException
     *
     * @expectedExceptionMessage The "--bits" option must be string
     *
     * @throws \ReflectionException
     */
    public function testGettingBitsOptionsThrowsException(): void
    {
        $bitsOptionTrait = $this->getObjectForTrait(BitsOptionTrait::class);

        $method = new ReflectionMethod(get_class($bitsOptionTrait), 'getBitsOption');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getOption('bits')->willReturn(['1024']);

        $method->invoke($bitsOptionTrait, $inputProphecy->reveal());
    }
}
