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
 * Bits option trait.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 */
trait BitsOptionTrait
{
    /**
     * Get bits option.
     *
     * @param InputInterface $input
     *
     * @return int
     *
     * @throws \Symfony\Component\Console\Exception\InvalidOptionException
     */
    protected function getBitsOption(InputInterface $input): int
    {
        $bits = $input->getOption('bits');
        if (is_string($bits)) {
            return (int)$bits;
        }

        throw new InvalidOptionException('The "--bits" option must be string');
    }
}
