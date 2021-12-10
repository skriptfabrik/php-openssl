<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Console\Command;

use Skriptfabrik\Openssl\Console\Input\BitsOptionTrait;
use Skriptfabrik\Openssl\Console\Input\CipherOptionTrait;
use Skriptfabrik\Openssl\Console\Input\DigestOptionTrait;
use Skriptfabrik\Openssl\Console\Input\NoOverrideOptionTrait;
use Skriptfabrik\Openssl\Console\Input\OutputArgumentTrait;
use Skriptfabrik\Openssl\Console\Input\PassphraseOptionTrait;
use Skriptfabrik\Openssl\Console\Input\TypeOptionTrait;
use Skriptfabrik\Openssl\Exception\InvalidArgumentException;
use Skriptfabrik\Openssl\Exception\OpensslErrorException;
use Skriptfabrik\Openssl\Generator\PrivateKeyGenerator;
use Skriptfabrik\Openssl\PrivateKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate private key command class.
 *
 * @package Skriptfabrik\Openssl\Console\Command
 */
class GeneratePrivateKeyCommand extends Command
{
    use DigestOptionTrait;
    use TypeOptionTrait;
    use BitsOptionTrait;
    use PassphraseOptionTrait;
    use CipherOptionTrait;
    use NoOverrideOptionTrait;
    use OutputArgumentTrait;

    /**
     * Command name.
     */
    public const NAME = 'openssl:generate-private-key';

    /**
     * Command description.
     */
    public const DESCRIPTION = 'Generate a private key with OpenSSL';

    /**
     * The default digest.
     */
    public const DEFAULT_DIGEST = 'sha256';

    /**
     * The default key type.
     */
    public const DEFAULT_TYPE = PrivateKey::TYPE_RSA;

    /**
     * The default number of bits.
     */
    public const DEFAULT_BITS = 2048;

    /**
     * Default name
     */
    protected static $defaultName = self::NAME;

    /**
     * Configure command.
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
    {
        $this->setName(self::NAME);
        $this->setDescription(self::DESCRIPTION);
        $this->addOption(
            'digest',
            'd',
            InputOption::VALUE_REQUIRED,
            'The method or signature hash',
            self::DEFAULT_DIGEST
        );
        $this->addOption(
            'type',
            't',
            InputOption::VALUE_REQUIRED,
            'The type of the private key',
            self::DEFAULT_TYPE
        );
        $this->addOption(
            'bits',
            'b',
            InputOption::VALUE_REQUIRED,
            'The size of the private key to generate in bits',
            (string)self::DEFAULT_BITS
        );
        $this->addOption(
            'passphrase',
            'p',
            InputOption::VALUE_REQUIRED,
            'The private key can be optionally protected by a passphrase'
        );
        $this->addOption(
            'cipher',
            'c',
            InputOption::VALUE_REQUIRED,
            'The cipher for the passphrase protection'
        );
        $this->addOption(
            'no-override',
            null,
            InputOption::VALUE_NONE,
            'Keep formerly generated private key file'
        );
        $this->addArgument(
            'output',
            InputArgument::OPTIONAL,
            'The filename of the private key to generate',
            getcwd() . '/private.pem'
        );
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $digest = $this->getDigestOption($input);
        $type = $this->getTypeOption($input);
        $bits = $this->getBitsOption($input);
        $passphrase = $this->getPassphraseOption($input);
        $cipher = $this->getCipherOption($input);
        $noOverride = $this->isNoOverrideOption($input);
        $outputFile = $this->getOutputArgument($input);

        if ($noOverride && $outputFile->isFile()) {
            $output->writeln(
                sprintf(
                    '[OpenSSL] Private key exists: %s',
                    $outputFile
                )
            );
            return 0;
        }

        if (!$outputFile->getPathInfo()->isWritable()) {
            $output->writeln(sprintf('<error>[OpenSSL] Unable to write private key file: %s</error>', $outputFile));
            return 1;
        }

        $generator = $this->createPrivateKeyGenerator();

        try {
            $generator
                ->setDigest($digest)
                ->setType($type)
                ->setBits($bits)
                ->setPassphrase($passphrase)
                ->setCipher($cipher);
        } catch (InvalidArgumentException $exception) {
            $output->writeln(sprintf('<error>[OpenSSL] %s</error>', $exception->getMessage()));
            return 1;
        }

        try {
            $key = $generator->generate();
            $encrypted = $key->isEncrypted();
            $type = $key->getType();
            $bits = $key->getBits();
            $key->exportToFile($outputFile);
        } catch (OpensslErrorException $exception) {
            $output->writeln(sprintf('<error>[OpenSSL] %s</error>', $exception->getMessage()));
            return 1;
        }

        $output->writeln(
            sprintf(
                '[OpenSSL] Generated %s%s private key with %s bits: %s',
                $encrypted ? 'encrypted ' : '',
                $type,
                $bits,
                $outputFile
            )
        );

        return 0;
    }

    /**
     * Create private key generator.
     *
     * @return PrivateKeyGenerator
     */
    public function createPrivateKeyGenerator(): PrivateKeyGenerator
    {
        return new PrivateKeyGenerator();
    }
}
