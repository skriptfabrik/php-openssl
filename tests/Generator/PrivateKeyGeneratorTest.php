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
     * @runInSeparateProcess
     */
    public function testSupportedDigests(): void
    {
        $digests = ['sha256'];

        $opensslGetMdMethods = $this->getFunctionMock(__NAMESPACE__, 'openssl_get_md_methods');
        $opensslGetMdMethods->expects($this->once())->willReturn($digests);

        $generator = new PrivateKeyGenerator();
        $this->assertSame($digests, $generator->getSupportedDigests());
        $this->assertSame($digests, $generator->getSupportedDigests()); // ensures that method gets called only once
    }

    /**
     * @throws \Skriptfabrik\Openssl\Exception\OpensslErrorException
     */
    public function testDefaultKeyGenerator(): void
    {
        $generator = new PrivateKeyGenerator();

        $this->assertSame(PrivateKey::TYPE_RSA, $generator->getType());
        $this->assertSame(2048, $generator->getBits());
        $this->assertNull($generator->getPassphrase());
        $this->assertNull($generator->getCipher());

        $key = $generator->generate();

        $this->assertSame(PrivateKey::TYPE_RSA, $key->getType());
        $this->assertSame(2048, $key->getBits());
    }

    /**
     * @throws \Skriptfabrik\Openssl\Exception\OpensslErrorException
     */
    public function testRsaKeyGenerator(): void
    {
        $generator = new PrivateKeyGenerator();
        $generator->setDigest('sha512');
        $generator->setType(PrivateKey::TYPE_RSA);
        $generator->setBits(512);
        $generator->setPassphrase('My super secret passphrase');
        $generator->setCipher('aes-256-cbc');

        $this->assertSame('sha512', $generator->getDigest());
        $this->assertSame(PrivateKey::TYPE_RSA, $generator->getType());
        $this->assertSame(512, $generator->getBits());
        $this->assertSame('My super secret passphrase', $generator->getPassphrase());
        $this->assertSame('aes-256-cbc', $generator->getCipher());

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
        $generator->setDigest('sha512');
        $generator->setType(PrivateKey::TYPE_DSA);
        $generator->setBits(1024);
        $generator->setPassphrase('My extra secret passphrase');
        $generator->setCipher(null);

        $this->assertSame('sha512', $generator->getDigest());
        $this->assertSame(PrivateKey::TYPE_DSA, $generator->getType());
        $this->assertSame(1024, $generator->getBits());
        $this->assertSame('My extra secret passphrase', $generator->getPassphrase());
        $this->assertNull($generator->getCipher());

        $key = $generator->generate();

        $this->assertSame(PrivateKey::TYPE_DSA, $key->getType());
        $this->assertSame(1024, $key->getBits());
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\InvalidArgumentException
     */
    public function testGeneratorThrowsExceptionOnInvalidDigest(): void
    {
        $generator = new PrivateKeyGenerator();
        $generator->setDigest('invalid');
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
     * @expectedException \Skriptfabrik\Openssl\Exception\InvalidArgumentException
     */
    public function testGeneratorThrowsExceptionOnInvalidPassphrase(): void
    {
        $generator = new PrivateKeyGenerator();
        $generator->setPassphrase('');
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\InvalidArgumentException
     */
    public function testGeneratorThrowsExceptionOnInvalidCipher(): void
    {
        $generator = new PrivateKeyGenerator();
        $generator->setCipher('invalid');
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
