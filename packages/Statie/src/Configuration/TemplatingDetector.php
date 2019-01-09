<?php declare(strict_types=1);

namespace Symplify\Statie\Configuration;

use PhpCsFixer\Finder;

final class TemplatingDetector
{
    /**
     * @var string|null
     */
    private $detectedTemplating;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function detect(): string
    {
        if ($this->detectedTemplating) {
            return $this->detectedTemplating;
        }

        $twigFileCount = $this->fileCountBySuffix('twig');
        $latteFileCount = $this->fileCountBySuffix('latte');

        $this->detectedTemplating = $twigFileCount > $latteFileCount ? 'twig' : 'latte';

        return $this->detectedTemplating;
    }

    private function fileCountBySuffix(string $suffix): int
    {
        $finder = Finder::create()
            ->files()
            ->in($this->configuration->getSourceDirectory())
            ->name('*.' . $suffix);

        return count(iterator_to_array($finder->getIterator()));
    }
}
