<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Console\Command;

use PHPUnit\Framework\TestCase;
use Skriptfabrik\Openssl\Console\Application;
use Skriptfabrik\Openssl\Helper\TempFileObjectHelper;
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
}
