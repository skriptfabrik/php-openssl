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
 * Passphrase option trait test.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 *
 * @covers  \Skriptfabrik\Openssl\Console\Input\PassphraseOptionTrait
 */
class PassphraseOptionTraitTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testGettingPassphraseOptions(): void
    {
        $passphraseOptionTrait = $this->getObjectForTrait(PassphraseOptionTrait::class);

        $method = new ReflectionMethod(get_class($passphraseOptionTrait), 'getPassphraseOption');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getOption('passphrase')->willReturn('My passphrase');

        $this->assertSame('My passphrase', $method->invoke($passphraseOptionTrait, $inputProphecy->reveal()));
    }

    /**
     * @expectedException \Symfony\Component\Console\Exception\InvalidOptionException
     *
     * @expectedExceptionMessage The "--passphrase" option must be string
     *
     * @throws \ReflectionException
     */
    public function testGettingPassphraseOptionsThrowsException(): void
    {
        $passphraseOptionTrait = $this->getObjectForTrait(PassphraseOptionTrait::class);

        $method = new ReflectionMethod(get_class($passphraseOptionTrait), 'getPassphraseOption');
        $method->setAccessible(true);

        $inputProphecy = $this->prophesize(InputInterface::class);
        $inputProphecy->getOption('passphrase')->willReturn(['My passphrase']);

        $method->invoke($passphraseOptionTrait, $inputProphecy->reveal());
    }
}
