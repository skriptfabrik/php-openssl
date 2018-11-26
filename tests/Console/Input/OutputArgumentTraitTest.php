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
 * Output argument trait test.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 *
 * @covers  \Skriptfabrik\Openssl\Console\Input\OutputArgumentTrait
 */
class OutputArgumentTraitTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testGettingOutputArguments(): void
    {
        $outputArgumentTrait = $this->getObjectForTrait(OutputArgumentTrait::class);

        $method = new ReflectionMethod(get_class($outputArgumentTrait), 'getOutputArgument');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getArgument('output')->willReturn('key.pem');

        $this->assertInstanceOf(SplFileInfo::class, $method->invoke($outputArgumentTrait, $inputProphecy->reveal()));
    }

    /**
     * @expectedException \Symfony\Component\Console\Exception\InvalidArgumentException
     *
     * @expectedExceptionMessage The "output" argument must be string
     *
     * @throws \ReflectionException
     */
    public function testGettingOutputArgumentsThrowsException(): void
    {
        $outputArgumentTrait = $this->getObjectForTrait(OutputArgumentTrait::class);

        $method = new ReflectionMethod(get_class($outputArgumentTrait), 'getOutputArgument');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getArgument('output')->willReturn(['key.pem']);

        $method->invoke($outputArgumentTrait, $inputProphecy->reveal());
    }
}
