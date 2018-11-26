<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

namespace PHPSTORM_META {

    use PHPUnit\Framework\TestCase;
    use Prophecy\Argument;
    use Psr\Container\ContainerInterface;

    override(
        ContainerInterface::get(),
        map(
            [
                '' => '@',
            ]
        )
    );

    override(
        TestCase::getMockForAbstractClass(),
        map(
            [
                '' => '@',
            ]
        )
    );

    override(
        TestCase::getMockForTrait(),
        map(
            [
                '' => '@',
            ]
        )
    );

    override(
        Argument::type(0),
        map(
            [
                '' => '@',
            ]
        )
    );
}
