<?php declare(strict_types=1);

namespace Symplify\Statie\Configuration\Parser;

use Symfony\Component\Yaml\Parser;

final class YamlParser
{
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
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
