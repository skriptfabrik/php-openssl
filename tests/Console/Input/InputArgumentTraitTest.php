<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Console\Input;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;
use function get_class;

/**
 * Input argument trait test.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 *
 * @covers  \Skriptfabrik\Openssl\Console\Input\InputArgumentTrait
 */
class InputArgumentTraitTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testGettingInputArguments(): void
    {
        $inputArgumentTrait = $this->getObjectForTrait(InputArgumentTrait::class);

        $method = new ReflectionMethod(get_class($inputArgumentTrait), 'getInputArgument');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getArgument('input')->willReturn('key.pem');

        $this->assertInstanceOf(SplFileInfo::class, $method->invoke($inputArgumentTrait, $inputProphecy->reveal()));
    }

    /**
     * @expectedException \Symfony\Component\Console\Exception\InvalidArgumentException
     *
     * @expectedExceptionMessage The "input" argument must be string
     *
     * @throws \ReflectionException
     */
    public function testGettingInputArgumentsThrowsException(): void
    {
        $inputArgumentTrait = $this->getObjectForTrait(InputArgumentTrait::class);

        $method = new ReflectionMethod(get_class($inputArgumentTrait), 'getInputArgument');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getArgument('input')->willReturn(['key.pem']);

        $method->invoke($inputArgumentTrait, $inputProphecy->reveal());
    }
}
