.PHONY: lint lint-fix install update test test-coverage phpstan analyze

MKDIR_BUILD = php -r "is_dir('build/coverage') || mkdir('build/coverage', 0777, true); is_dir('build/logs') || mkdir('build/logs', 0777, true);"

lint:
	php vendor/bin/phpcs --standard=PSR12 --extensions=php src/ tests/

lint-fix:
	php vendor/bin/phpcbf --standard=PSR12 --extensions=php src/ tests/

# Запуск тестов с генерацией отчётов для SonarQube
test:
	@$(MKDIR_BUILD)
	php vendor/bin/phpunit --coverage-clover build/coverage/clover.xml --log-junit build/logs/junit.xml

test-coverage:
	@$(MKDIR_BUILD)
	php vendor/bin/phpunit --coverage-html build/coverage/html --coverage-clover build/coverage/clover.xml --log-junit build/logs/junit.xml

# Статический анализ с PHPStan
phpstan:
	php vendor/bin/phpstan analyze --configuration phpstan.neon

# Полный анализ кода (lint + phpstan)
analyze: lint phpstan

# Установка зависимостей
install:
	composer install

# Обновление зависимостей
update:
	composer update
