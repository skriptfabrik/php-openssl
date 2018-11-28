<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Skriptfabrik\Openssl\Helper\OpensslKeyResourceHelper;
use Skriptfabrik\Openssl\Helper\TempFileObjectHelper;
use function get_class;

/**
 * Private key test class.
 *
 * @package Skriptfabrik\Openssl
 *
 * @covers  \Skriptfabrik\Openssl\PrivateKey
 */
class PrivateKeyTest extends TestCase
{
    use PHPMock;

    /**
     * @var string
     */
    private $pem = <<<EOT
-----BEGIN PRIVATE KEY-----
MIIBUwIBADANBgkqhkiG9w0BAQEFAASCAT0wggE5AgEAAkEApod3FBaODkjLIcIL
kQMn/exD409iCsta5YbCsA8OkPxio47ioP3Teq2knjdb7NPnGrHqwcYiPI0dMK95
2RGmDwIDAQABAkBbzRG34TcuaLKSSYZWyoahVD2YcYp6qN/S6BcrNyGwio1PYDj3
wmBp1NmgnEeT48z/6rkOE6ax5ao6LT967DEBAiEA2sAhHX1OOuU23ibb8TDZyUcQ
y/+yPAvka86BG+lFimMCIQDC4uEgWXVgfYEuZGG1OGG8EkVWIs9GENOTdCecAhTP
ZQIgcpBbB4m/teKj2Lb7S5ctCFgauOxCWWZVDA0L2yVRkUcCIHbH4X7eB3RwCjRE
amkRnEAMwsXlIYAK8WmF+j5T5mshAiAgpjstTwz6Ie/5lXlRH8NEwURDsGkbQnO+
eu8vnLCcHw==
-----END PRIVATE KEY-----

EOT;

    /**
     * @var mixed[]
     */
    private $config = [
        'digest_alg' => 'sha512',
        'private_key_bits' => 4096,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ];

    /**
     * @throws \ReflectionException
     */
    public function testPrivateKey(): void
    {
        $key = new PrivateKey(OpensslKeyResourceHelper::createPrivateKeyResource($this->config));

        $method = new ReflectionMethod(get_class($key), 'getKey');
        $method->setAccessible(true);

        $this->assertInternalType('resource', $method->invoke($key));
        $this->assertFalse($key->isEncrypted());
    }

    /**
     * @throws \Skriptfabrik\Openssl\Exception\OpensslErrorException
     * @throws \ReflectionException
     */
    public function testPrivateKeyFromString(): void
    {
        $key = PrivateKey::fromString($this->pem);

        $method = new ReflectionMethod(get_class($key), 'getKey');
        $method->setAccessible(true);

        $this->assertInternalType('resource', $method->invoke($key));
        $this->assertFalse($key->isEncrypted());
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\OpensslErrorException
     *
     * @runInSeparateProcess
     */
    public function testPrivateKeyFromStringThrowsException(): void
    {
        $opensslPkeyGetPrivate = $this->getFunctionMock(__NAMESPACE__, 'openssl_pkey_get_private');
        $opensslPkeyGetPrivate->expects($this->once())->willReturn(false);

        $opensslErrorString = $this->getFunctionMock(__NAMESPACE__, 'openssl_error_string');
        $opensslErrorString->expects($this->once())->willReturn('Unknown OpenSSL error');

        PrivateKey::fromString($this->pem);
    }

    /**
     * @throws \Skriptfabrik\Openssl\Exception\OpensslErrorException
     * @throws \ReflectionException
     */
    public function testPrivateKeyFromFile(): void
    {
        $file = TempFileObjectHelper::createTempFile();
        $file->fwrite($this->pem);

        $key = PrivateKey::fromFile($file);

        $method = new ReflectionMethod(get_class($key), 'getKey');
        $method->setAccessible(true);

        $this->assertInternalType('resource', $method->invoke($key));
        $this->assertFalse($key->isEncrypted());

        unlink($file->getPathname());
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\OpensslErrorException
     *
     * @runInSeparateProcess
     */
    public function testPrivateKeyFromFileThrowsException(): void
    {
        $opensslPkeyGetPrivate = $this->getFunctionMock(__NAMESPACE__, 'openssl_pkey_get_private');
        $opensslPkeyGetPrivate->expects($this->once())->willReturn(false);

        $opensslErrorString = $this->getFunctionMock(__NAMESPACE__, 'openssl_error_string');
        $opensslErrorString->expects($this->once())->willReturn('Unknown OpenSSL error');

        $file = TempFileObjectHelper::createTempFile();
        $file->fwrite($this->pem);

        PrivateKey::fromFile($file);

        unlink($file->getPathname());
    }

    /**
     * @throws \Skriptfabrik\Openssl\Exception\OpensslErrorException
     */
    public function testGettingPublicKey(): void
    {
        $key = new PrivateKey(OpensslKeyResourceHelper::createPrivateKeyResource($this->config));

        $this->assertInstanceOf(PublicKey::class, $key->getPublicKey());
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\OpensslErrorException
     *
     * @runInSeparateProcess
     */
    public function testGettingPublicKeyThrowsException(): void
    {
        $opensslPkeyGetDetails = $this->getFunctionMock(__NAMESPACE__, 'openssl_pkey_get_details');
        $opensslPkeyGetDetails->expects($this->once())->willReturn(false);

        $opensslErrorString = $this->getFunctionMock(__NAMESPACE__, 'openssl_error_string');
        $opensslErrorString->expects($this->once())->willReturn('Unknown OpenSSL error');

        $key = new PrivateKey(OpensslKeyResourceHelper::createPrivateKeyResource($this->config));

        $key->getPublicKey();
    }

    /**
     * @throws \Skriptfabrik\Openssl\Exception\OpensslErrorException
     */
    public function testExport(): void
    {
        $key = PrivateKey::fromString($this->pem);

        $this->assertEquals($this->pem, $key->export());
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\OpensslErrorException
     *
     * @runInSeparateProcess
     */
    public function testExportThrowsException(): void
    {
        $opensslPkeyExport = $this->getFunctionMock(__NAMESPACE__, 'openssl_pkey_export');
        $opensslPkeyExport->expects($this->once())->willReturn(false);

        $opensslErrorString = $this->getFunctionMock(__NAMESPACE__, 'openssl_error_string');
        $opensslErrorString->expects($this->once())->willReturn('Unknown OpenSSL error');

        $key = PrivateKey::fromString($this->pem);

        $key->export();
    }

    /**
     * @throws \Skriptfabrik\Openssl\Exception\OpensslErrorException
     */
    public function testExportToFile(): void
    {
        $inputFile = TempFileObjectHelper::createTempFile();
        $inputFile->fwrite($this->pem);

        $outputFile = TempFileObjectHelper::createTempFile();

        $key = PrivateKey::fromFile($inputFile);
        $key->exportToFile($outputFile);

        $this->assertFileEquals($inputFile->getPathname(), $outputFile->getPathname());

        unlink($inputFile->getPathname());
        unlink($outputFile->getPathname());
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\OpensslErrorException
     *
     * @runInSeparateProcess
     */
    public function testExportToFileThrowsException(): void
    {
        $opensslPkeyExportToFile = $this->getFunctionMock(__NAMESPACE__, 'openssl_pkey_export_to_file');
        $opensslPkeyExportToFile->expects($this->once())->willReturn(false);

        $opensslErrorString = $this->getFunctionMock(__NAMESPACE__, 'openssl_error_string');
        $opensslErrorString->expects($this->once())->willReturn('Unknown OpenSSL error');

        $outputFile = TempFileObjectHelper::createTempFile();

        $key = PrivateKey::fromString($this->pem);

        try {
            $key->exportToFile($outputFile);
        } finally {
            unlink($outputFile->getPathname());
        }
    }
}
