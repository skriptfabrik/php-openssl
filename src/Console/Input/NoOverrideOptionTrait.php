<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Console\Input;

use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use function is_bool;

/**
 * No override option trait.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 */
trait NoOverrideOptionTrait
{
    /**
     * Whether no override option is set.
     *
     * @param InputInterface $input
     *
     * @return bool
     *
     * @throws \Symfony\Component\Console\Exception\InvalidOptionException
     */
    protected function isNoOverrideOption(InputInterface $input): bool
    {
        $noOverride = $input->getOption('no-override');
        if (is_bool($noOverride)) {
            return $noOverride;
        }

        throw new InvalidOptionException('The "--no-override" option must be boolean');
    }
}
