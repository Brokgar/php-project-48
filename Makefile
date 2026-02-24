.PHONY: lint lint-fix


lint:
	vendor\bin\phpcs.bat --standard=PSR12 --extensions=php src/ tests/

lint-fix:
	vendor\bin\phpcbf.bat --standard=PSR12 --extensions=php src/ tests/

# Запуск тестов (если есть)
test:
	./vendor/bin/phpunit

# Установка зависимостей
install:
	composer install

# Обновление зависимостей
update:
	composer update
