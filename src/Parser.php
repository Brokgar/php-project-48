<?php

namespace Hexlet\Gendiff;

class Parser
{
    private FileReader $fileReader;
    private DataParser $dataParser;

    public function __construct(
        ?FileReader $fileReader = null,
        ?DataParser $dataParser = null
    ) {
        $this->fileReader = $fileReader ?? new FileReader();
        $this->dataParser = $dataParser ?? new DataParser();
    }

    public function parseFile(string $filePath): array
    {
        $absolutePath = $this->fileReader->resolvePath($filePath);
        $content = $this->fileReader->read($absolutePath, $filePath);

        $extension = pathinfo($absolutePath, PATHINFO_EXTENSION);

        return $this->dataParser->parseToArray($content, $extension);
    }
}
