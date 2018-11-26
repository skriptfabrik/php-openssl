<?php declare(strict_types=1);
/**
 * This file is part of the skriptfabrik PHP OpenSSL package.
 *
 * @author Daniel Schröder <daniel.schroeder@skriptfabrik.com>
 */

foreach ([
             __DIR__ . '/../../../autoload.php',
             __DIR__ . '/../../vendor/autoload.php',
             __DIR__ . '/../vendor/autoload.php'
         ] as $file) {
    if (file_exists($file)) {
        define('COMPOSER_AUTOLOAD_FILE', $file);

        break;
    }
}

if (!defined('COMPOSER_AUTOLOAD_FILE')) {
    fwrite(
        STDERR,
        'You need to set up the project dependencies using Composer:' . PHP_EOL . PHP_EOL .
        '    composer install' . PHP_EOL . PHP_EOL .
        'You can learn all about Composer on https://getcomposer.org/.' . PHP_EOL
    );

    die(1);
}

/** @noinspection PhpIncludeInspection */
require COMPOSER_AUTOLOAD_FILE;

$application = new Skriptfabrik\Openssl\Console\Application();
$application->addCommands([
    new Skriptfabrik\Openssl\Console\Command\GeneratePrivateKeyCommand(),
    new Skriptfabrik\Openssl\Console\Command\ExportPublicKeyCommand(),
]);

return $application->run();
