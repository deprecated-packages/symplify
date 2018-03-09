<?php declare(strict_types=1);

namespace Symplify\Statie\Configuration\Parser;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;
use Symplify\Statie\Exception\Yaml\InvalidYamlSyntaxException;

final class YamlParser
{
    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * @return mixed[]
     */
    public function decodeFile(string $filePath): array
    {
        return $this->parser->parseFile($filePath);
    }

    /**
     * @return mixed[]
     */
    public function decode(string $content): array
    {
        return $this->parser->parse($content);
    }
}
