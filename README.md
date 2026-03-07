# Gendiff - утилита для сравнения конфигурационных файлов

[![Actions Status](https://github.com/Brokgar/php-project-48/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/Brokgar/php-project-48/actions)
[![CI](https://github.com/Brokgar/php-project-48/actions/workflows/ci.yml/badge.svg)](https://github.com/Brokgar/php-project-48/actions/workflows/ci.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Brokgar_php-project-48&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=Brokgar_php-project-48)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=Brokgar_php-project-48&metric=bugs)](https://sonarcloud.io/summary/new_code?id=Brokgar_php-project-48)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=Brokgar_php-project-48&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=Brokgar_php-project-48)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=Brokgar_php-project-48&metric=coverage)](https://sonarcloud.io/summary/new_code?id=Brokgar_php-project-48)

## Описание

`gendiff` сравнивает два файла конфигурации и показывает различия.

Поддерживаемые форматы входных файлов:
- JSON (`.json`)
- YAML (`.yaml`, `.yml`)

Поддерживаемые форматы вывода:
- `stylish` (по умолчанию)
- `plain`
- `json`

## Требования

- PHP `^8.2`
- Composer
- Make (для команд из `Makefile`)
- Xdebug (только для покрытия в `make test` и `make test-coverage`)

## Установка

```bash
git clone https://github.com/Brokgar/php-project-48.git
cd php-project-48
make install
```

## Использование

### CLI

```bash
# Базовое сравнение (формат stylish по умолчанию)
php bin/gendiff tests/fixtures/file1.json tests/fixtures/file2.json

# Явно указать формат
php bin/gendiff -f plain tests/fixtures/file1.json tests/fixtures/file2.json
php bin/gendiff --format json tests/fixtures/file1.yaml tests/fixtures/file2.yaml

# Справка
php bin/gendiff --help
```

### Как библиотека

```php
use Hexlet\Gendiff\Gendiff;

$result = Gendiff::compareFiles('file1.json', 'file2.json', 'stylish');
echo $result;
```

Или через хелпер-функцию:

```php
use function Hexlet\Gendiff\genDiff;

$result = genDiff('file1.json', 'file2.json', 'stylish');
echo $result;
```

## Разработка

```bash
# Линтинг
make lint

# Автоисправление стиля
make lint-fix

# Тесты (с формированием отчетов для Sonar)
make test

# Тесты + HTML-отчет покрытия
make test-coverage

# Статический анализ
make phpstan

# Полный анализ (lint + phpstan)
make analyze
```

Альтернатива через Composer scripts:

```bash
composer test
composer lint
composer phpstan
composer analyze
```

Отчеты после `make test`/`make test-coverage`:
- `build/coverage/clover.xml`
- `build/logs/junit.xml`
- `build/coverage/html` (только для `make test-coverage`)

## Демонстрация

- Plain: https://asciinema.org/a/819827
- Stylish: https://asciinema.org/a/819826
- JSON: https://asciinema.org/a/819828

## Вклад

Правила и процесс: [CONTRIBUTING.md](CONTRIBUTING.md)

## Лицензия

MIT
