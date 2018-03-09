<?php declare(strict_types=1);

namespace Symplify\Statie\Configuration\Parser;

use Symfony\Component\Yaml\Exception\ParseException;
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
        } catch (ParseException $parseException) {
            throw new InvalidYamlSyntaxException(sprintf(
                'Invalid YAML syntax found in "%s" file: %s',
                $filePath,
                $parseException->getMessage()
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
