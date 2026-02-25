# Gendiff - Утилита для сравнения конфигурационных файлов

[![Actions Status](https://github.com/Brokgar/php-project-48/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/Brokgar/php-project-48/actions)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Brokgar_php-project-48&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=Brokgar_php-project-48)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=Brokgar_php-project-48&metric=bugs)](https://sonarcloud.io/summary/new_code?id=Brokgar_php-project-48)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=Brokgar_php-project-48&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=Brokgar_php-project-48)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=Brokgar_php-project-48&metric=coverage)](https://sonarcloud.io/summary/new_code?id=Brokgar_php-project-48)

## Описание

Gendiff — это PHP-утилита командной строки для сравнения двух конфигурационных файлов (JSON) и отображения различий в различных форматах:

- `plain` — простой текстовый формат
- `stylish` — красивый формат с отступами и иконками
- `json` — JSON-представление различий

Поддерживается цветной вывод при запуске в терминале, поддерживающем ANSI-коды.

## Требования

* Операционная система: Linux, Macos, WSL или Windows с поддержкой командной строки
* PHP >= 8.2
* Xdebug (для покрытия кода)
* Composer
* Make
* Git

## Установка

1. Клонируйте репозиторий:
```bash
git clone https://github.com/Brokgar/php-project-48.git
```

2. Перейдите в директорию проекта:
```bash
cd php-project-48
```

3. Установите зависимости:
```bash
make install
```

## Использование

### Через командную строку

```bash
# Простое сравнение
php bin/gendiff tests/fixtures/file1.json tests/fixtures/file2.json

# С указанием формата вывода
php bin/gendiff -f stylish tests/fixtures/file1.json tests/fixtures/file2.json

# С цветным выводом
php bin/gendiff --color -f stylish tests/fixtures/file1.json tests/fixtures/file2.json

# Показать справку
php bin/gendiff --help
```

### Как библиотека

```php
use Hexlet\Gendiff\Gendiff;

$options = [
    'format' => 'stylish',
    'color' => true
];

$result = Gendiff::compareFiles('file1.json', 'file2.json', $options);
echo $result;
```

## Доступные форматы вывода

| Формат | Описание |
|--------|----------|
| `plain` | Простой текст, только изменения |
| `stylish` | Красивый формат с отступами и символами изменений |
| `json` | Структурированный JSON-вывод |

## Разработка

### Запуск линтера

```bash
make lint
```

Конфигурации линтеров находятся в:
- `phpcs.xml` — для PHP_CodeSniffer

### Запуск тестов

```bash
# Запуск всех тестов
make test

# Запуск тестов с отчётом о покрытии
make test-coverage
```

### Сборка и анализ кода

Проект интегрирован с SonarQube/SonarCloud. Для анализа кода:

```bash
# Убедитесь, что директории build существуют
mkdir -p build/coverage build/logs

# Запустите тесты с генерацией отчётов
make test
```

Отчёты будут сохранены в:
- `build/coverage/clover.xml` — покрытие кода
- `build/logs/junit.xml` — результаты тестов

## Лицензия

MIT