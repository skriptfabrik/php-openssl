<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Console\Command;

use PHPUnit\Framework\TestCase;
use Skriptfabrik\Openssl\Console\Application;
use Skriptfabrik\Openssl\Exception\OpensslErrorException;
use Skriptfabrik\Openssl\Helper\TempFileObjectHelper;
use Skriptfabrik\Openssl\PrivateKey;
use SplFileInfo;
use SplFileObject;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Export public key command test class.
 *
 * @package Skriptfabrik\Openssl\Console\Command
 *
 * @covers  \Skriptfabrik\Openssl\Console\Command\ExportPublicKeyCommand
 */
class ExportPublicKeyCommandTest extends TestCase
{
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

    public function testConfiguration(): void
    {
        $command = new ExportPublicKeyCommand();

        $this->assertSame(ExportPublicKeyCommand::NAME, $command->getName());
        $this->assertSame(ExportPublicKeyCommand::DESCRIPTION, $command->getDescription());
        $this->assertTrue($command->getDefinition()->hasArgument('input'));
        $this->assertTrue($command->getDefinition()->hasArgument('output'));
    }

    public function testSuccessfulExecution(): void
    {
        $application = new Application();
        $application->add(new ExportPublicKeyCommand());

        $command = $application->find(ExportPublicKeyCommand::NAME);
        $commandTester = new CommandTester($command);

        $input = TempFileObjectHelper::createTempFile();
        $input->fwrite($this->pem);

        $output = TempFileObjectHelper::createTempFile();

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'input' => $input->getPathname(),
                'output' => $output->getPathname(),
            ]
        );

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertContains('[OpenSSL] Exported RSA public key with 512 bits', $commandTester->getDisplay());

        unlink($input->getPathname());
        unlink($output->getPathname());
    }

    public function testSuccessfulExecutionWithNoOverride(): void
    {
        $application = new Application();
        $application->add(new ExportPublicKeyCommand());

        $command = $application->find(ExportPublicKeyCommand::NAME);
        $commandTester = new CommandTester($command);

        $input = TempFileObjectHelper::createTempFile();
        $input->fwrite($this->pem);

        $output = TempFileObjectHelper::createTempFile();
        $output->fwrite('');

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'input' => $input->getPathname(),
                'output' => $output->getPathname(),
                '--no-override' => true,
            ]
        );

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertContains('[OpenSSL] Public key exists', $commandTester->getDisplay());

        unlink($input->getPathname());
        unlink($output->getPathname());
    }

    public function testExecutionReturnsErrorOnReadFailure(): void
    {
        $inputFileProphecy = $this->prophesize(SplFileInfo::class);
        $inputFileProphecy->isReadable()->willReturn(false);
        $inputFileProphecy->__toString()->willReturn('private.pem');

        $commandMock = $this->getMockBuilder(ExportPublicKeyCommand::class)
            ->setMethods(['getInputArgument'])
            ->getMock();

        $commandMock
            ->method('getInputArgument')
            ->willReturn($inputFileProphecy->reveal());

        $application = new Application();
        $application->add($commandMock);

        $command = $application->find(ExportPublicKeyCommand::NAME);
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
            ]
        );

        $this->assertEquals(1, $commandTester->getStatusCode());
        $this->assertContains('[OpenSSL] Unable to read private key file', $commandTester->getDisplay());
    }

    public function testExecutionReturnsErrorOnWriteFailure(): void
    {
        $inputFileProphecy = $this->prophesize(SplFileInfo::class);
        $inputFileProphecy->isReadable()->willReturn(true);

        $pathInfoProphecy = $this->prophesize(SplFileInfo::class);
        $pathInfoProphecy->isWritable()->willReturn(false);

        $outputFileProphecy = $this->prophesize(SplFileInfo::class);
        $outputFileProphecy->isFile()->willReturn(false);
        $outputFileProphecy->getPathInfo()->willReturn($pathInfoProphecy->reveal());
        $outputFileProphecy->__toString()->willReturn('public.pem');

        $commandMock = $this->getMockBuilder(ExportPublicKeyCommand::class)
            ->setMethods(['getInputArgument', 'getOutputArgument'])
            ->getMock();

        $commandMock
            ->method('getInputArgument')
            ->willReturn($inputFileProphecy->reveal());

        $commandMock
            ->method('getOutputArgument')
            ->willReturn($outputFileProphecy->reveal());

        $application = new Application();
        $application->add($commandMock);

        $command = $application->find(ExportPublicKeyCommand::NAME);
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
            ]
        );

        $this->assertEquals(1, $commandTester->getStatusCode());
        $this->assertContains('[OpenSSL] Unable to write public key file', $commandTester->getDisplay());
    }

    /**
     * @throws OpensslErrorException
     */
    public function testExecutionReturnsErrorOnDetailsFailure(): void
    {
        $inputFileProphecy = $this->prophesize(SplFileInfo::class);
        $inputFileProphecy->isReadable()->willReturn(true);
        $inputFileProphecy->openFile()->willReturn($this->prophesize(SplFileObject::class)->reveal());

        $privateKeyProphecy = $this->prophesize(PrivateKey::class);
        $privateKeyProphecy->getPublicKey()->willThrow(new OpensslErrorException('Unknown OpenSSL error'));

        $commandMock = $this->getMockBuilder(ExportPublicKeyCommand::class)
            ->setMethods(['getInputArgument', 'createPrivateKeyFromFile'])
            ->getMock();

        $commandMock
            ->method('getInputArgument')
            ->willReturn($inputFileProphecy->reveal());

        $commandMock
            ->method('createPrivateKeyFromFile')
            ->willReturn($privateKeyProphecy->reveal());

        $application = new Application();
        $application->add($commandMock);

        $command = $application->find(ExportPublicKeyCommand::NAME);
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
            ]
        );

        $this->assertEquals(1, $commandTester->getStatusCode());
        $this->assertContains('[OpenSSL] Unknown OpenSSL error', $commandTester->getDisplay());
    }
}
