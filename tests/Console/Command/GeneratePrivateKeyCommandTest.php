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
use Skriptfabrik\Openssl\Generator\PrivateKeyGenerator;
use Skriptfabrik\Openssl\Helper\TempFileObjectHelper;
use Skriptfabrik\Openssl\KeyInterface;
use SplFileInfo;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Generate private key command test class.
 *
 * @package Skriptfabrik\Openssl\Console\Command
 *
 * @covers  \Skriptfabrik\Openssl\Console\Command\GeneratePrivateKeyCommand
 */
class GeneratePrivateKeyCommandTest extends TestCase
{
    public function testConfiguration(): void
    {
        $command = new GeneratePrivateKeyCommand();

        $this->assertSame(GeneratePrivateKeyCommand::NAME, $command->getName());
        $this->assertSame(GeneratePrivateKeyCommand::DESCRIPTION, $command->getDescription());
        $this->assertTrue($command->getDefinition()->hasOption('type'));
        $this->assertTrue($command->getDefinition()->hasOption('bits'));
        $this->assertTrue($command->getDefinition()->hasArgument('output'));
    }

    public function testSuccessfulExecution(): void
    {
        $application = new Application();
        $application->add(new GeneratePrivateKeyCommand());

        $command = $application->find(GeneratePrivateKeyCommand::NAME);
        $commandTester = new CommandTester($command);

        $output = TempFileObjectHelper::createTempFile();

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'output' => $output->getPathname(),
                '--type' => 'DSA',
                '--bits' => '1024',
            ]
        );

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertContains('[OpenSSL] Generated DSA private key with 1024 bits', $commandTester->getDisplay());

        unlink($output->getPathname());
    }

    public function testSuccessfulExecutionWithNoOverride(): void
    {
        $application = new Application();
        $application->add(new GeneratePrivateKeyCommand());

        $command = $application->find(GeneratePrivateKeyCommand::NAME);
        $commandTester = new CommandTester($command);

        $output = TempFileObjectHelper::createTempFile();
        $output->fwrite('');

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'output' => $output->getPathname(),
                '--no-override' => true,
            ]
        );

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertContains('[OpenSSL] Private key exists', $commandTester->getDisplay());

        unlink($output->getPathname());
    }

    public function testExecutionReturnsErrorOnWriteFailure(): void
    {
        $pathInfoProphecy = $this->prophesize(SplFileInfo::class);
        $pathInfoProphecy->isWritable()->willReturn(false);

        $outputFileProphecy = $this->prophesize(SplFileInfo::class);
        $outputFileProphecy->isFile()->willReturn(false);
        $outputFileProphecy->getPathInfo()->willReturn($pathInfoProphecy->reveal());
        $outputFileProphecy->__toString()->willReturn('private.pem');

        $commandMock = $this->getMockBuilder(GeneratePrivateKeyCommand::class)
            ->setMethods(['getOutputArgument'])
            ->getMock();

        $commandMock
            ->method('getOutputArgument')
            ->willReturn($outputFileProphecy->reveal());

        $application = new Application();
        $application->add($commandMock);

        $command = $application->find(GeneratePrivateKeyCommand::NAME);
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
            ]
        );

        $this->assertEquals(1, $commandTester->getStatusCode());
        $this->assertContains('[OpenSSL] Unable to write private key file', $commandTester->getDisplay());
    }

    public function testExecutionReturnsErrorOnInvalidTypeOption(): void
    {
        $application = new Application();
        $application->add(new GeneratePrivateKeyCommand());

        $command = $application->find(GeneratePrivateKeyCommand::NAME);
        $commandTester = new CommandTester($command);

        $output = TempFileObjectHelper::createTempFile();
        $output->fwrite('');

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'output' => $output->getPathname(),
                '--type' => 'FOO',
            ]
        );

        $this->assertEquals(1, $commandTester->getStatusCode());
        $this->assertContains('[OpenSSL] Invalid key type (FOO)', $commandTester->getDisplay());

        unlink($output->getPathname());
    }

    public function testExecutionReturnsErrorOnInvalidBitsOption(): void
    {
        $application = new Application();
        $application->add(new GeneratePrivateKeyCommand());

        $command = $application->find(GeneratePrivateKeyCommand::NAME);
        $commandTester = new CommandTester($command);

        $output = TempFileObjectHelper::createTempFile();
        $output->fwrite('');

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'output' => $output->getPathname(),
                '--bits' => '8',
            ]
        );

        $this->assertEquals(1, $commandTester->getStatusCode());
        $this->assertContains('[OpenSSL] Invalid number of bits (8)', $commandTester->getDisplay());

        unlink($output->getPathname());
    }

    /**
     * @throws OpensslErrorException
     */
    public function testExecutionReturnsErrorOnGeneratorFailure(): void
    {
        // phpcs:ignore
        $returnSelf = function () {
            return func_get_arg(1);
        };

        $privateKeyGeneratorProphecy = $this->prophesize(PrivateKeyGenerator::class);
        $privateKeyGeneratorProphecy->setType(KeyInterface::TYPE_RSA)->will($returnSelf);
        $privateKeyGeneratorProphecy->setBits(2048)->will($returnSelf);
        $privateKeyGeneratorProphecy->setPassphrase(null)->will($returnSelf);
        $privateKeyGeneratorProphecy->generate()->willThrow(new OpensslErrorException('Unknown OpenSSL error'));

        $commandMock = $this->getMockBuilder(GeneratePrivateKeyCommand::class)
            ->setMethods(['createPrivateKeyGenerator'])
            ->getMock();

        $commandMock
            ->method('createPrivateKeyGenerator')
            ->willReturn($privateKeyGeneratorProphecy->reveal());

        $application = new Application();
        $application->add($commandMock);

        $command = $application->find(GeneratePrivateKeyCommand::NAME);
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
