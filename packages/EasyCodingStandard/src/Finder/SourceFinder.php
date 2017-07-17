<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use SplFileInfo;
use Symplify\EasyCodingStandard\Contract\Finder\ExtraFilesProviderInterface;

final class SourceFinder
{
    /**
     * @var ExtraFilesProviderInterface[]
     */
    private $extraFilesProvider = [];

    public function addExtraFilesProvider(ExtraFilesProviderInterface $sourceProvider): void
    {
        $this->extraFilesProvider[] = $sourceProvider;
    }

    /**
     * @param string[]
     * @return SplFileInfo[]
     */
    public function find(array $source): array
    {
        $files = [];

        foreach ($this->extraFilesProvider as $extraFilesProvider) {
            $files = array_merge($files, $extraFilesProvider->provideForSource($source));
        }

        return $files;
    }
}
