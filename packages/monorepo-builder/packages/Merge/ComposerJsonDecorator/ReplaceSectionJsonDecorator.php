<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Configuration\MergedPackagesCollector;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerJsonDecoratorInterface;

final class ReplaceSectionJsonDecorator implements ComposerJsonDecoratorInterface
{
    public function __construct(
        private MergedPackagesCollector $mergedPackagesCollector
    ) {
    }

    public function decorate(ComposerJson $composerJson): void
    {
        $mergedPackages = $this->mergedPackagesCollector->getPackages();

        foreach ($mergedPackages as $mergedPackage) {
            // prevent value override
            if ($composerJson->isReplacePackageSet($mergedPackage)) {
                continue;
            }

            $composerJson->setReplacePackage($mergedPackage, 'self.version');
        }
    }
}
