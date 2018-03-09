<?php declare(strict_types=1);

namespace Symplify\Statie\Configuration\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml;
use Symplify\Statie\Exception\Yaml\InvalidYamlSyntaxException;

final class YamlParser
{
    /**
     * @return mixed[]
     */
    public function decodeFromFile(string $filePath): array
    {
        $fileContent = file_get_contents($filePath);

        try {
            return $this->decode($fileContent);
        } catch (Exception $exception) {
            throw new InvalidYamlSyntaxException(sprintf(
                'Invalid YAML syntax found in "%s" file: %s',
                $filePath,
                $exception->getMessage()
            ));
        }
    }

    /**
     * @return mixed[]
     */
    public function decode(string $content): array
    {
        return Yaml::parse($content);
    }
}
