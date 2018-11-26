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
 * Type option trait.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 */
trait TypeOptionTrait
{
    /**
     * Get type option.
     *
     * @param InputInterface $input
     *
     * @return string
     *
     * @throws \Symfony\Component\Console\Exception\InvalidOptionException
     */
    protected function getTypeOption(InputInterface $input): string
    {
        $type = $input->getOption('type');
        if (is_string($type)) {
            return $type;
        }

        throw new InvalidOptionException('The "--type" option must be string');
    }
}
