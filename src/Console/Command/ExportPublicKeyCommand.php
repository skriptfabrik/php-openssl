<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Console\Command;

use Skriptfabrik\Openssl\Console\Input\InputArgumentTrait;
use Skriptfabrik\Openssl\Console\Input\NoOverrideOptionTrait;
use Skriptfabrik\Openssl\Console\Input\OutputArgumentTrait;
use Skriptfabrik\Openssl\Console\Input\PassphraseOptionTrait;
use Skriptfabrik\Openssl\Exception\OpensslErrorException;
use Skriptfabrik\Openssl\PrivateKey;
use SplFileObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function getcwd;
use function sprintf;

/**
 * Generate public key command class.
 *
 * @package Skriptfabrik\Openssl\Console\Command
 */
class ExportPublicKeyCommand extends Command
{
    use PassphraseOptionTrait;
    use NoOverrideOptionTrait;
    use InputArgumentTrait;
    use OutputArgumentTrait;

    /**
     * Command name.
     */
    public const NAME = 'openssl:export-public-key';

    /**
     * Command description.
     */
    public const DESCRIPTION = 'Export the public key with OpenSSL';

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
            'passphrase',
            'p',
            InputOption::VALUE_REQUIRED,
            'The optional parameter passphrase must be used if the specified private key is protected by a passphrase.'
        );
        $this->addOption(
            'no-override',
            null,
            InputOption::VALUE_NONE,
            'Keep formerly generated public key file'
        );
        $this->addArgument(
            'input',
            InputArgument::OPTIONAL,
            'The filename of the private key to use',
            getcwd() . '/private.pem'
        );
        $this->addArgument(
            'output',
            InputArgument::OPTIONAL,
            'The filename of the public key to export',
            getcwd() . '/public.pem'
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
        $passphrase = $this->getPassphraseOption($input);
        $noOverride = $this->isNoOverrideOption($input);
        $inputFile = $this->getInputArgument($input);
        $outputFile = $this->getOutputArgument($input);

        if ($noOverride && $outputFile->isFile()) {
            $output->writeln(
                sprintf(
                    '[OpenSSL] Public key exists: %s',
                    $outputFile
                )
            );

            return 0;
        }

        if (!$inputFile->isReadable()) {
            $output->writeln(sprintf('<error>[OpenSSL] Unable to read private key file: %s</error>', $inputFile));

            return 1;
        }

        if (!$outputFile->getPathInfo()->isWritable()) {
            $output->writeln(sprintf('<error>[OpenSSL] Unable to write public key file: %s</error>', $outputFile));

            return 1;
        }

        try {
            $key = $this->createPrivateKeyFromFile($inputFile->openFile(), $passphrase)->getPublicKey();
            $type = $key->getType();
            $bits = $key->getBits();
            $key->exportToFile($outputFile);
        } catch (OpensslErrorException $exception) {
            $output->writeln(sprintf('<error>[OpenSSL] %s</error>', $exception->getMessage()));

            return 1;
        }

        $output->writeln(
            sprintf(
                '[OpenSSL] Exported %s public key with %s bits: %s',
                $type,
                $bits,
                $outputFile
            )
        );

        return 0;
    }

    /**
     * Create private key from file.
     *
     * @param SplFileObject $file
     * @param string|null $passphrase
     *
     * @return PrivateKey
     *
     * @throws OpensslErrorException
     */
    public function createPrivateKeyFromFile(SplFileObject $file, ?string $passphrase = null): PrivateKey
    {
        return PrivateKey::fromFile($file, $passphrase);
    }
}
