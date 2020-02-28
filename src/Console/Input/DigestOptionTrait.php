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
 * Digest option trait.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 */
trait DigestOptionTrait
{
    /**
     * Get digest option.
     *
     * @param InputInterface $input
     *
     * @return string
     *
     * @throws \Symfony\Component\Console\Exception\InvalidOptionException
     */
    protected function getDigestOption(InputInterface $input): string
    {
        $digest = $input->getOption('digest');
        if (is_string($digest)) {
            return $digest;
        }

        throw new InvalidOptionException('The "--digest" option must be string');
    }
}
