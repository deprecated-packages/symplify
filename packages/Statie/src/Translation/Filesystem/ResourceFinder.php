<?php declare(strict_types=1);

namespace Symplify\Statie\Translation\Filesystem;

use Nette\Utils\Finder;
use Nette\Utils\Strings;
use SplFileInfo;

final class ResourceFinder
{
    /**
     * @return string[][]
     */
    public function findInDirectory(string $directory): array
    {
        if (! is_dir($directory)) {
            return [];
        }

        $finder = Finder::findFiles('*.neon')->in($directory);
        $resources = [];

        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            if (! $resource = $this->matchResource($file)) {
                continue;
            }

            $resources[] = $this->createResource($resource, $file);
        }

        return $resources;
    }

    /**
     * @return mixed
     */
    private function matchResource(SplFileInfo $fileInfo)
    {
        return Strings::match(
            $fileInfo->getFilename(),
            '~^(?P<domain>.*?)\.(?P<locale>[^\.]+)\.(?P<format>[^\.]+)$~'
        );
    }

    /**
     * @param mixed[] $resource
     * @return string[]
     */
    private function createResource(array $resource, SplFileInfo $filefileInfo): array
    {
        return [
            'format' => $resource['format'],
            'pathname' => $filefileInfo->getPathname(),
            'locale' => $resource['locale'],
            'domain' => $resource['domain'],
        ];
    }
}
