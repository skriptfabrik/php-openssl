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
 * Type option trait test.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 *
 * @covers  \Skriptfabrik\Openssl\Console\Input\TypeOptionTrait
 */
class TypeOptionTraitTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testGettingTypeOptions(): void
    {
        $typeOptionTrait = $this->getObjectForTrait(TypeOptionTrait::class);

        $method = new ReflectionMethod(get_class($typeOptionTrait), 'getTypeOption');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getOption('type')->willReturn('RSA');

        $this->assertSame('RSA', $method->invoke($typeOptionTrait, $inputProphecy->reveal()));
    }

    /**
     * @expectedException \Symfony\Component\Console\Exception\InvalidOptionException
     *
     * @expectedExceptionMessage The "--type" option must be string
     *
     * @throws \ReflectionException
     */
    public function testGettingTypeOptionsThrowsException(): void
    {
        $typeOptionTrait = $this->getObjectForTrait(TypeOptionTrait::class);

        $method = new ReflectionMethod(get_class($typeOptionTrait), 'getTypeOption');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getOption('type')->willReturn(['RSA']);

        $method->invoke($typeOptionTrait, $inputProphecy->reveal());
    }
}
