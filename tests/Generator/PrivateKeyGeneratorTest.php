<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Generator;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use Skriptfabrik\Openssl\PrivateKey;

/**
 * Private key generator test class.
 *
 * @package Skriptfabrik\Openssl\Generator
 *
 * @covers  \Skriptfabrik\Openssl\Generator\PrivateKeyGenerator
 */
class PrivateKeyGeneratorTest extends TestCase
{
    use PHPMock;

    /**
     * @throws \Skriptfabrik\Openssl\Exception\OpensslErrorException
     */
    public function testRsaKeyGenerator(): void
    {
        $generator = new PrivateKeyGenerator();
        $generator->setType(PrivateKey::TYPE_RSA);
        $generator->setBits(512);

        $this->assertSame(PrivateKey::TYPE_RSA, $generator->getType());
        $this->assertSame(512, $generator->getBits());

        $key = $generator->generate();

        $this->assertSame(PrivateKey::TYPE_RSA, $key->getType());
        $this->assertSame(512, $key->getBits());
    }

    /**
     * @throws \Skriptfabrik\Openssl\Exception\OpensslErrorException
     */
    public function testDsaKeyGenerator(): void
    {
        $generator = new PrivateKeyGenerator();
        $generator->setType(PrivateKey::TYPE_DSA);
        $generator->setBits(1024);

        $this->assertSame(PrivateKey::TYPE_DSA, $generator->getType());
        $this->assertSame(1024, $generator->getBits());

        $key = $generator->generate();

        $this->assertSame(PrivateKey::TYPE_DSA, $key->getType());
        $this->assertSame(1024, $key->getBits());
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\InvalidArgumentException
     */
    public function testGeneratorThrowsExceptionOnInvalidType(): void
    {
        $generator = new PrivateKeyGenerator();
        $generator->setType('INVALID');
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\InvalidArgumentException
     */
    public function testGeneratorThrowsExceptionOnInvalidBits(): void
    {
        $generator = new PrivateKeyGenerator();
        $generator->setBits(0);
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\OpensslErrorException
     *
     * @runInSeparateProcess
     */
    public function testGeneratorThrowsExceptionOnGenerate(): void
    {
        $opensslPkeyNew = $this->getFunctionMock(__NAMESPACE__, 'openssl_pkey_new');
        $opensslPkeyNew->expects($this->once())->willReturn(false);

        $opensslErrorString = $this->getFunctionMock(__NAMESPACE__, 'openssl_error_string');
        $opensslErrorString->expects($this->once())->willReturn('Unknown OpenSSL error');

        $generator = new PrivateKeyGenerator();
        $generator->generate();
    }
}
