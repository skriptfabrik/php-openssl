<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Console\Input;

use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use function is_string;

/**
 * Passphrase option trait.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 */
trait PassphraseOptionTrait
{
    /**
     * Get passphrase option.
     *
     * @param InputInterface $input
     *
     * @return string|null
     *
     * @throws \Symfony\Component\Console\Exception\InvalidOptionException
     */
    protected function getPassphraseOption(InputInterface $input): ?string
    {
        $passphrase = $input->getOption('passphrase');
        if ($passphrase === null || is_string($passphrase)) {
            return $passphrase;
        }

        throw new InvalidOptionException('The "--passphrase" option must be string');
    }
}
