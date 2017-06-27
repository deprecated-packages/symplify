<?php declare(strict_types=1);

namespace Symplify\Statie\Configuration\Parser;

use Nette\Neon\Exception;
use Nette\Neon\Neon;
use Symplify\Statie\Exception\Neon\InvalidNeonSyntaxException;

final class NeonParser
{
    /**
     * @return mixed[]
     */
    public function decodeFromFile(string $filePath): array
    {
        $fileContent = file_get_contents($filePath);

        try {
            return $this->decode($fileContent);
        } catch (Exception $neonException) {
            throw new InvalidNeonSyntaxException(sprintf(
                'Invalid NEON syntax found in "%s" file: %s',
                $filePath,
                $neonException->getMessage()
            ));
        }
    }

    /**
     * @return mixed[]
     */
    public function decode(string $content): array
    {
        return Neon::decode($content);
    }
}
