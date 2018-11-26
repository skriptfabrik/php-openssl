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
 * Public key test class.
 *
 * @package Skriptfabrik\Openssl
 *
 * @covers  \Skriptfabrik\Openssl\PublicKey
 */
class PublicKeyTest extends TestCase
{
    use PHPMock;

    /**
     * @var string
     */
    private $pem = <<<EOT
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAKaHdxQWjg5IyyHCC5EDJ/3sQ+NPYgrL
WuWGwrAPDpD8YqOO4qD903qtpJ43W+zT5xqx6sHGIjyNHTCvedkRpg8CAwEAAQ==
-----END PUBLIC KEY-----

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
    public function testPublicKey(): void
    {
        $key = new PublicKey(OpensslKeyResourceHelper::createPublicKeyResource($this->config));

        $method = new ReflectionMethod(get_class($key), 'getKey');
        $method->setAccessible(true);

        $this->assertInternalType('resource', $method->invoke($key));
    }

    /**
     * @throws \Skriptfabrik\Openssl\Exception\OpensslErrorException
     * @throws \ReflectionException
     */
    public function testPublicKeyFromString(): void
    {
        $key = PublicKey::fromString($this->pem);

        $method = new ReflectionMethod(get_class($key), 'getKey');
        $method->setAccessible(true);

        $this->assertInternalType('resource', $method->invoke($key));
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\OpensslErrorException
     *
     * @runInSeparateProcess
     */
    public function testPublicKeyFromStringThrowsException(): void
    {
        $opensslPkeyGetPublic = $this->getFunctionMock(__NAMESPACE__, 'openssl_pkey_get_public');
        $opensslPkeyGetPublic->expects($this->once())->willReturn(false);

        $opensslErrorString = $this->getFunctionMock(__NAMESPACE__, 'openssl_error_string');
        $opensslErrorString->expects($this->once())->willReturn('Unknown OpenSSL error');

        PublicKey::fromString($this->pem);
    }

    /**
     * @throws \Skriptfabrik\Openssl\Exception\OpensslErrorException
     * @throws \ReflectionException
     */
    public function testPublicKeyFromFile(): void
    {
        $file = TempFileObjectHelper::createTempFile();
        $file->fwrite($this->pem);

        $key = PublicKey::fromFile($file);

        $method = new ReflectionMethod(get_class($key), 'getKey');
        $method->setAccessible(true);

        $this->assertInternalType('resource', $method->invoke($key));

        unlink($file->getPathname());
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\OpensslErrorException
     *
     * @runInSeparateProcess
     */
    public function testPublicKeyFromFileThrowsException(): void
    {
        $opensslPkeyGetPublic = $this->getFunctionMock(__NAMESPACE__, 'openssl_pkey_get_public');
        $opensslPkeyGetPublic->expects($this->once())->willReturn(false);

        $opensslErrorString = $this->getFunctionMock(__NAMESPACE__, 'openssl_error_string');
        $opensslErrorString->expects($this->once())->willReturn('Unknown OpenSSL error');

        $file = TempFileObjectHelper::createTempFile();
        $file->fwrite($this->pem);

        PublicKey::fromFile($file);

        unlink($file->getPathname());
    }

    /**
     * @throws \Skriptfabrik\Openssl\Exception\OpensslErrorException
     */
    public function testExport(): void
    {
        $key = PublicKey::fromString($this->pem);

        $this->assertEquals($this->pem, $key->export());
    }

    /**
     * @expectedException \Skriptfabrik\Openssl\Exception\OpensslErrorException
     *
     * @runInSeparateProcess
     */
    public function testExportThrowsException(): void
    {
        $opensslPkeyGetDetails = $this->getFunctionMock(__NAMESPACE__, 'openssl_pkey_get_details');
        $opensslPkeyGetDetails->expects($this->once())->willReturn(false);

        $opensslErrorString = $this->getFunctionMock(__NAMESPACE__, 'openssl_error_string');
        $opensslErrorString->expects($this->once())->willReturn('Unknown OpenSSL error');

        $key = PublicKey::fromString($this->pem);

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

        $key = PublicKey::fromFile($inputFile);
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
        $opensslPkeyGetDetails = $this->getFunctionMock(__NAMESPACE__, 'openssl_pkey_get_details');
        $opensslPkeyGetDetails->expects($this->once())->willReturn(false);

        $opensslErrorString = $this->getFunctionMock(__NAMESPACE__, 'openssl_error_string');
        $opensslErrorString->expects($this->once())->willReturn('Unknown OpenSSL error');

        $outputFile = TempFileObjectHelper::createTempFile();

        $key = PublicKey::fromString($this->pem);

        try {
            $key->exportToFile($outputFile);
        } finally {
            unlink($outputFile->getPathname());
        }
    }
}
