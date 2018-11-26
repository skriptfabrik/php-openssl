<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Abstract key test class.
 *
 * @package Skriptfabrik\Openssl
 *
 * @covers  \Skriptfabrik\Openssl\AbstractKey
 */
class AbstractKeyTest extends TestCase
{
    use PHPMock;

    /**
     * @return mixed[][]
     */
    public function provideKeys(): array
    {
        $keys = [
            openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_RSA, 'private_key_bits' => 512]),
            openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_DSA, 'private_key_bits' => 1024]),
            openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_DH, 'private_key_bits' => 1024]),
        ];

        return [
            [$keys[0], AbstractKey::TYPE_RSA, 512],
            [$keys[1], AbstractKey::TYPE_DSA, 1024],
            [$keys[2], AbstractKey::TYPE_UNSUPPORTED, 1024],
        ];
    }

    /**
     * @dataProvider provideKeys
     *
     * @param resource $key
     * @param string $type
     * @param int $bits
     *
     * @throws \Skriptfabrik\Openssl\Exception\OpensslErrorException
     * @throws \ReflectionException
     */
    public function testGetters($key, string $type, int $bits): void
    {
        $abstractKey = $this->getAbstractKeyMock(
            [
                'getKey',
            ]
        );

        $abstractKey
            ->method('getKey')
            ->willReturn($key);

        $this->assertSame($type, $abstractKey->getType());
        $this->assertSame($bits, $abstractKey->getBits());
    }

    /**
     * Get abstract key mock.
     *
     * @param string[] $methods
     *
     * @return AbstractKey|MockObject
     *
     * @throws \ReflectionException
     */
    public function getAbstractKeyMock(array $methods = []): object
    {
        return $this->getMockForAbstractClass(
            AbstractKey::class,
            [],
            '',
            true,
            true,
            true,
            $methods
        );
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\OpensslErrorException
     *
     * @runInSeparateProcess
     *
     * @throws \ReflectionException
     */
    public function testTypeGetterThrowsException(): void
    {
        $opensslPkeyGetDetails = $this->getFunctionMock(__NAMESPACE__, 'openssl_pkey_get_details');
        $opensslPkeyGetDetails->expects($this->once())->willReturn(false);

        $opensslErrorString = $this->getFunctionMock(__NAMESPACE__, 'openssl_error_string');
        $opensslErrorString->expects($this->once())->willReturn('Unknown OpenSSL error');

        $abstractKey = $this->getAbstractKeyMock(
            [
                'getKey',
            ]
        );

        $abstractKey
            ->method('getKey')
            ->willReturn(null);

        $abstractKey->getType();
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\OpensslErrorException
     *
     * @runInSeparateProcess
     *
     * @throws \ReflectionException
     */
    public function testBitsGetterThrowsException(): void
    {
        $opensslPkeyGetDetails = $this->getFunctionMock(__NAMESPACE__, 'openssl_pkey_get_details');
        $opensslPkeyGetDetails->expects($this->once())->willReturn(false);

        $opensslErrorString = $this->getFunctionMock(__NAMESPACE__, 'openssl_error_string');
        $opensslErrorString->expects($this->once())->willReturn('Unknown OpenSSL error');

        $abstractKey = $this->getAbstractKeyMock(
            [
                'getKey',
            ]
        );

        $abstractKey
            ->method('getKey')
            ->willReturn(null);

        $abstractKey->getBits();
    }
}
