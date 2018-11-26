# skriptfabrik PHP OpenSSL package

[![Build Status](https://travis-ci.com/skriptfabrik/php-openssl.svg?branch=master)](https://travis-ci.com/skriptfabrik/php-openssl)
[![Coverage Status](https://coveralls.io/repos/github/skriptfabrik/php-openssl/badge.svg?branch=master)](https://coveralls.io/github/skriptfabrik/php-openssl?branch=master)

## Usage

Run `$ vendor/bin/openssl help [<command_name>]` to display the usage details for a specific command.

### Generate a private key

The default private key file with the name `private.pem` will be generated to the current working directory. You can
specify the `--type` and `--bits` as an option. If you want to keep an existing private key, append the `--no-override`
option to the command.

```bash
$ vendor/bin/openssl openssl:generate-private-key [<output>]
```

### Export the public key with OpenSSL

The default public key file with the name `public.pem` will be exported to the current working directory. The private
key is expected to be named `private.pem`. It should also be stored in the working directory per default. If you want to
keep an existing public key, append the `--no-override` option to the command.

```bash
$ vendor/bin/openssl openssl:export-public-key [<input>] [<output>]
```

## Development

### Requirements

The development of this project is powered by [Docker](https://www.docker.com/), Docker Compose and
[GNU Make](http://www.gnu.org/software/make). Please have a look at the [Makefile](Makefile) for exact commands.

### Install dependencies and development tools

This will install all Composer dependencies and development tools. 

```bash
$ make install
```

### Analyse code

The code analysis is being performed with PHPStan.

```bash
$ make analysis
```

### Check code style

The code style is being checked with PHP_CodeSniffer.

```bash
$ make style-check
```

### Fix code style

Some minor code style issues can be fixed with PHP_CodeSniffer.

```bash
$ make style-fix
```

### Run Tests

```bash
$ make tests
```

### Run Tests With Coverage

```bash
$ make tests-with-coverage
```
