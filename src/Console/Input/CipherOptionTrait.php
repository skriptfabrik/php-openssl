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
 * Cipher option trait.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 */
trait CipherOptionTrait
{
    /**
     * Get cipher option.
     *
     * @param InputInterface $input
     *
     * @return string|null
     *
     * @throws \Symfony\Component\Console\Exception\InvalidOptionException
     */
    protected function getCipherOption(InputInterface $input): ?string
    {
        $cipher = $input->getOption('cipher');
        if ($cipher === null || is_string($cipher)) {
            return $cipher;
        }

        throw new InvalidOptionException('The "--cipher" option must be string');
    }
}
