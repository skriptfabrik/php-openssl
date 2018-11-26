<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace Skriptfabrik\Openssl\Console\Input;

use SplFileInfo;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use function is_string;

/**
 * Output argument trait.
 *
 * @package Skriptfabrik\Openssl\Console\Input
 */
trait OutputArgumentTrait
{
    /**
     * Get output argument.
     *
     * @param InputInterface $input
     *
     * @return SplFileInfo
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function getOutputArgument(InputInterface $input): SplFileInfo
    {
        $outputFile = $input->getArgument('output');
        if (is_string($outputFile)) {
            return new SplFileInfo($outputFile);
        }

        throw new InvalidArgumentException('The "output" argument must be string');
    }
}
