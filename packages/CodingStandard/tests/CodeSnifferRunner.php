<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests;

use PHP_CodeSniffer;
use Symplify\CodingStandard\Tests\Exception\FileNotFoundException;

final class CodeSnifferRunner
{
    /**
     * @var PHP_CodeSniffer
     */
    private $codeSniffer;

    public function __construct(string $sniff)
    {
        $this->codeSniffer = new PHP_CodeSniffer();
        $this->codeSniffer->initStandard(__DIR__ . '/../src/SymplifyCodingStandard/ruleset.xml', [$sniff]);
    }

    public function getErrorCountInFile(string $source) : int
    {
        $this->ensureFileExists($source);

        $file = $this->codeSniffer->processFile($source);

        return $file->getErrorCount();
    }

    public function getFixedContent(string $source) : string
    {
        $this->ensureFileExists($source);

        $file = $this->codeSniffer->processFile($source);
        $file->fixer->fixFile();

        return $file->fixer->getContents();
    }

    private function ensureFileExists(string $source)
    {
        if (! file_exists($source)) {
            throw new FileNotFoundException(
                sprintf('File "%s" was not found.', $source)
            );
        }
    }
}
