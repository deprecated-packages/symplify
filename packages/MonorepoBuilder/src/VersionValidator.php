<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Composer\Section;
use Symplify\MonorepoBuilder\Exception\AmbiguousVersionException;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;

final class VersionValidator
{
    /**
     * @var mixed[]
     */
    private $requiredPackages = [];

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    public function __construct(JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }

    /**
     * @param SplFileInfo[] $fileInfos
     */
    public function validateFileInfos(array $fileInfos): void
    {
        foreach ($fileInfos as $fileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($fileInfo);

            foreach ($this->requiredPackages as $packageName => $packageVersion) {
                $this->processSection($json, $packageName, $packageVersion, $fileInfo, Section::REQUIRE);
                $this->processSection($json, $packageName, $packageVersion, $fileInfo, Section::REQUIRE_DEV);
            }

            $this->requiredPackages += $json[Section::REQUIRE] ?? [];
            $this->requiredPackages += $json[Section::REQUIRE_DEV] ?? [];
        }
    }

    /**
     * @param mixed[] $json
     */
    private function processSection(
        array $json,
        string $packageName,
        string $packageVersion,
        SplFileInfo $composerPackageFile,
        string $section
    ): void {
        if ($this->shouldSkip($json, $packageName, $packageVersion, $section)) {
            return;
        }

        throw new AmbiguousVersionException(sprintf(
            'Version "%s" for package "%s" is different than previously found "%s" in "%s" file',
            $json[$section][$packageName],
            $packageName,
            $packageVersion,
            $composerPackageFile->getPathname()
        ));
    }

    /**
     * @param mixed[] $json
     */
    private function shouldSkip(array $json, string $packageName, string $packageVersion, string $section): bool
    {
        if (! isset($json[$section][$packageName])) {
            return true;
        }

        return $json[$section][$packageName] === $packageVersion;
    }
}
