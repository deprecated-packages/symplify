<?php

declare(strict_types=1);

namespace Symplify\Statie\Configuration\Parser;

use Nette\Neon\Neon;

final class NeonParser
{
    public function decodeFromFile(string $filePath) : array
    {
        $fileContent = file_get_contents($filePath);

        return $this->decode($fileContent);
    }

    public function decode(string $content) : array
    {
        return Neon::decode($content);
    }
}
