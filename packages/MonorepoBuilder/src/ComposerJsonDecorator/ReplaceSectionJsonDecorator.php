<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\Configuration\MergedPackagesCollector;
use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;

final class ReplaceSectionJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @var MergedPackagesCollector
     */
    private $mergedPackagesCollector;

    public function __construct(MergedPackagesCollector $mergedPackagesCollector)
    {
        $this->mergedPackagesCollector = $mergedPackagesCollector;
    }

    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        $mergedPackages = $this->mergedPackagesCollector->getPackages();
        sort($mergedPackages);

        foreach ($mergedPackages as $mergedPackage) {
            $composerJson['replace'][$mergedPackage] = 'self.version';
        }

        return $composerJson;
    }
}
