export COMPOSER_HOME=${HOME}/.composer/cache/files

.PHONY: install
install:
	@echo "Installing dependencies and development tools"
	@docker-compose run --entrypoint /bin/sh --rm composer -c " \
		composer install && \
		composer bin phpunit install && \
		composer bin phpstan install && \
		composer bin squizlabs install && \
		composer bin php-coveralls install \
	"

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

.PHONY: travis-coverage-report
travis-coverage-report:
	@echo "Reporting code coverage"
	@docker-compose run -e TRAVIS=${TRAVIS} -e TRAVIS_JOB_ID=${TRAVIS_JOB_ID} --rm composer vendor/bin/php-coveralls
