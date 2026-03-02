.PHONY: lint lint-fix install update test test-coverage phpstan analyze

lint:
	vendor\bin\phpcs.bat --standard=PSR12 --extensions=php src/ tests/

lint-fix:
	vendor\bin\phpcbf.bat --standard=PSR12 --extensions=php src/ tests/

# Запуск тестов с генерацией отчётов для SonarQube
test:
	-@mkdir build\coverage 2>nul
	-@mkdir build\logs 2>nul
	vendor\bin\phpunit.bat --coverage-clover build/coverage/clover.xml --log-junit build/logs/junit.xml

test-coverage:
	-@mkdir build\coverage 2>nul
	-@mkdir build\logs 2>nul
	vendor\bin\phpunit.bat --coverage-html build/coverage/html --coverage-clover build/coverage/clover.xml --log-junit build/logs/junit.xml

# Статический анализ с PHPStan
phpstan:
	vendor\bin\phpstan analyze --configuration phpstan.neon

# Полный анализ кода (lint + phpstan)
analyze: lint phpstan

# Установка зависимостей
install:
	composer install

# Обновление зависимостей
update:
	composer update
