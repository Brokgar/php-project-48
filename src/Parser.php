<?php

namespace Hexlet\Gendiff;

class Parser
{
    public function __construct(
        private ?FileReader $fileReader = null,
        private ?DataParser $dataParser = null
    ) {
        $this->fileReader ??= new FileReader();
        $this->dataParser ??= new DataParser();
    }

    public function parseFile(string $filePath): array
    {
        $absolutePath = $this->fileReader->resolvePath($filePath);
        $content = $this->fileReader->read($absolutePath, $filePath);

        $extension = pathinfo($absolutePath, PATHINFO_EXTENSION);

        return $this->dataParser->parseToArray($content, $extension);
    }
}
