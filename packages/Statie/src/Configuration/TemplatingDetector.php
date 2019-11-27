<?php declare(strict_types=1);

namespace Symplify\Statie\Configuration;

use Symfony\Component\Finder\Finder;

final class TemplatingDetector
{
    /**
     * @var string|null
     */
    private $detectedTemplating;

    /**
     * @var StatieConfiguration
     */
    private $statieConfiguration;

    public function __construct(StatieConfiguration $statieConfiguration)
    {
        $this->statieConfiguration = $statieConfiguration;
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
            ->in($this->statieConfiguration->getSourceDirectory())
            ->name('*.' . $suffix);

        return count(iterator_to_array($finder->getIterator()));
    }
}
