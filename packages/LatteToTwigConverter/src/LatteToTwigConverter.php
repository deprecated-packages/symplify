<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter;

use Nette\Utils\FileSystem;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;
use Symplify\LatteToTwigConverter\Exception\ConfigurationException;

final class LatteToTwigConverter
{
    /**
     * @var CaseConverterInterface[]
     */
    private $caseConverters = [];

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

    public function convertFile(string $file): string
    {
        $content = FileSystem::read($file);

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
            'Duplicate case converter priority: %s and %s',
            get_class($caseConverter),
            get_class($this->caseConverters[$caseConverter->getPriority()])
        ));
    }
}
