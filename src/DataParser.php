<?php

namespace Hexlet\Gendiff;

use Hexlet\Gendiff\Exception\ParseException;
use JsonException;
use stdClass;
use Symfony\Component\Yaml\Exception\ParseException as YamlParseException;
use Symfony\Component\Yaml\Yaml;

class DataParser
{
    /**
     * Single source of truth for extension routing.
     *
     * @var array<string, string>
     */
    private const EXTENSION_PARSERS = [
        'json' => 'parseJson',
        'yaml' => 'parseYaml',
        'yml' => 'parseYaml',
    ];

    public function parseToArray(string $content, string $extension): array
    {
        $data = $this->parse($content, $extension, true);

        return is_array($data) ? $data : [];
    }

    public function parseToObject(string $content, string $extension): stdClass
    {
        $data = $this->parse($content, $extension, false);

        if ($data instanceof stdClass) {
            return $data;
        }

        if (is_array($data)) {
            $decoded = json_decode((string) json_encode($data), false, 512, JSON_THROW_ON_ERROR);
            return $decoded instanceof stdClass ? $decoded : new stdClass();
        }

        return new stdClass();
    }

    private function parse(string $content, string $extension, bool $asArray): mixed
    {
        $parserMethod = self::EXTENSION_PARSERS[strtolower($extension)] ?? null;
        if ($parserMethod === null) {
            throw new ParseException("Неподдерживаемый формат");
        }

        $normalizedContent = $this->normalizeContent($content);

        return match ($parserMethod) {
            'parseJson' => $this->parseJson($normalizedContent, $asArray),
            'parseYaml' => $this->parseYaml($normalizedContent, $asArray),
        };
    }

    private function normalizeContent(string $content): string
    {
        $boms = [
            "\xEF\xBB\xBF" => ['length' => 3, 'encoding' => null],
            "\xFF\xFE" => ['length' => 2, 'encoding' => 'UTF-16LE'],
            "\xFE\xFF" => ['length' => 2, 'encoding' => 'UTF-16BE'],
        ];

        $normalizedContents = array_values(array_filter(array_map(
            function (string $bom) use ($boms, $content): ?string {
                if (!str_starts_with($content, $bom)) {
                    return null;
                }

                $trimmedContent = substr($content, $boms[$bom]['length']);
                $encoding = $boms[$bom]['encoding'];

                return is_string($encoding)
                    ? mb_convert_encoding($trimmedContent, 'UTF-8', $encoding)
                    : $trimmedContent;
            },
            array_keys($boms)
        ), static fn (?string $normalizedContent): bool => $normalizedContent !== null));

        return $normalizedContents[0] ?? $content;
    }

    private function parseJson(string $content, bool $asArray): mixed
    {
        try {
            return json_decode($content, $asArray, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ParseException("Ошибка парсинга JSON: {$e->getMessage()}", 0, $e);
        }
    }

    private function parseYaml(string $content, bool $asArray): mixed
    {
        try {
            $data = Yaml::parse($content);
        } catch (YamlParseException $e) {
            throw new ParseException("Ошибка парсинга YAML: {$e->getMessage()}", 0, $e);
        }

        if ($asArray) {
            return is_array($data) ? $data : [];
        }

        if ($data instanceof stdClass) {
            return $data;
        }

        if (is_array($data)) {
            $decoded = json_decode((string) json_encode($data), false, 512, JSON_THROW_ON_ERROR);
            $data = $decoded instanceof stdClass ? $decoded : new stdClass();
        }

        return new stdClass();
    }
}
