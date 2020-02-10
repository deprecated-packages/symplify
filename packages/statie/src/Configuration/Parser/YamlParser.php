<?php

declare(strict_types=1);

namespace Symplify\Statie\Configuration\Parser;

use Symfony\Component\Yaml\Exception\ParseException;
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
    public function decodeInSource(string $content, string $source): array
    {
        try {
            return (array) $this->parser->parse($content);
        } catch (ParseException $parseException) {
            throw new ParseException(sprintf(
                'Invalid YAML syntax found in "%s": %s',
                $source,
                $parseException->getMessage()
            ), $parseException->getParsedLine(), null, null, $parseException);
        }
    }

    /**
     * @return mixed[]
     */
    public function decode(string $content): array
    {
        return $this->parser->parse($content);
    }
}
