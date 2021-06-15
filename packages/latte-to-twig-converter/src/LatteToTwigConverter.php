<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter;

use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;
use Symplify\LatteToTwigConverter\Exception\ConfigurationException;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\LatteToTwigConverter\Tests\LatteToTwigConverterTest
 */
final class LatteToTwigConverter
{
    /**
     * @var CaseConverterInterface[]
     */
    private array $caseConverters = [];

    /**
     * @param CaseConverterInterface[] $caseConverters
     */
    public function __construct(array $caseConverters)
    {
        foreach ($caseConverters as $caseConverter) {
            $this->ensureCaseConverterPriorityIsUnique($caseConverter);
            $this->caseConverters[$caseConverter->getPriority()] = $caseConverter;
        }

        krsort($this->caseConverters);
    }

    public function convertFile(SmartFileInfo $fileInfo): string
    {
        $content = $fileInfo->getContents();

        foreach ($this->caseConverters as $caseConverter) {
            $content = $caseConverter->convertContent($content);
        }

        return $content;
    }

    private function ensureCaseConverterPriorityIsUnique(CaseConverterInterface $caseConverter): void
    {
        if (! isset($this->caseConverters[$caseConverter->getPriority()])) {
            return;
        }

        throw new ConfigurationException(sprintf(
            'Duplicate case converter priority: "%s" and "%s"',
            $caseConverter::class,
            $this->caseConverters[$caseConverter->getPriority()]::class
        ));
    }
}
