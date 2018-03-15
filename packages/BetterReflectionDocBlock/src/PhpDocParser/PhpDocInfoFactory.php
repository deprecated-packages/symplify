<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

final class PhpDocInfoFactory
{
    /**
     * @var PhpDocParser
     */
    private $phpDocParser;

    public function __construct(PhpDocParser $phpDocParser)
    {
        $this->phpDocParser = $phpDocParser;
    }

    public function createFrom(string $content): PhpDocInfo
    {
        $phpDocNode = $this->phpDocParser->parse($content);
        $isSingleLine = $this->isSingleLine($content);

        return new PhpDocInfo($phpDocNode, $isSingleLine);
    }

    private function isSingleLine(string $content): bool
    {
        return substr_count($content, PHP_EOL) <= 1;
    }
}
