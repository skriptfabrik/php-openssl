export COMPOSER_HOME=${HOME}/.composer/cache/files

.PHONY: install
install:
	@echo "Installing dependencies"
	@docker-compose run --rm composer composer install
	@echo 'Installing development tools'
	@docker-compose run --rm composer composer bin all install

.PHONY: analysis
analysis:
	@echo "Analysing code"
	@docker-compose run --rm php php vendor/bin/phpstan analyse src tests

.PHONY: style-check
style-check:
	@echo "Checking code style"
	@docker-compose run --rm php php vendor/bin/phpcs -p

.PHONY: style-fix
style-fix:
	@echo "Fixing code style"
	@docker-compose run --rm php php vendor/bin/phpcbf -p

.PHONY: tests
tests:
	@echo "Running tests"
	@docker-compose run --rm php php vendor/bin/phpunit

.PHONY: tests-with-coverage
tests-with-coverage:
	@echo "Running tests with code coverage"
	@docker-compose run --entrypoint /bin/sh --rm php -c " \
		echo "zend_extension=xdebug.so" > \$${PHP_INI_DIR}/conf.d/xdebug.ini && \
		php vendor/bin/phpunit --coverage-clover clover.xml --coverage-text \
	"

.PHONY: coverage-report
coverage-report:
	@echo "Reporting code coverage"
	@docker-compose run --rm php php vendor/bin/codacycoverage clover clover.xml
