<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Renderable\File;

use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Utils\PathAnalyzer;

final class GeneratorFileFactory
{
    /**
     * Matches "id: <25>"
     * @var string
     */
    private const ID_PATTERN = '#^id:[\s]*(?<id>\d+)#m';

    /**
     * @var PathAnalyzer
     */
    private $pathAnalyzer;

    /**
     * @var GeneratorFileGuard
     */
    private $generatorFileGuard;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        PathAnalyzer $pathAnalyzer,
        GeneratorFileGuard $generatorFileGuard,
        Configuration $configuration
    ) {
        $this->pathAnalyzer = $pathAnalyzer;
        $this->generatorFileGuard = $generatorFileGuard;
        $this->configuration = $configuration;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return AbstractGeneratorFile[]
     */
    public function createFromFileInfosAndClass(array $fileInfos, string $class): array
    {
        $objects = [];

        $this->generatorFileGuard->ensureIsAbstractGeneratorFile($class);

        foreach ($fileInfos as $fileInfo) {
            $generatorFile = $this->createFromClassNameAndFileInfo($class, $fileInfo);
            $objects[$generatorFile->getId()] = $generatorFile;
        }

        return $objects;
    }

    private function createFromClassNameAndFileInfo(
        string $className,
        SmartFileInfo $smartFileInfo
    ): AbstractGeneratorFile {
        $id = $this->findAndGetValidId($smartFileInfo, $className);

        return new $className(
            $id,
            $smartFileInfo,
            $smartFileInfo->getRelativeFilePathFromDirectory($this->configuration->getSourceDirectory()),
            $smartFileInfo->getPathname(),
            $this->pathAnalyzer->detectFilenameWithoutDate($smartFileInfo),
            $this->pathAnalyzer->detectDate($smartFileInfo)
        );
    }

    private function findAndGetValidId(SplFileInfo $fileInfo, string $className): int
    {
        $match = Strings::match($fileInfo->getContents(), self::ID_PATTERN);
        $this->generatorFileGuard->ensureIdIsSet($fileInfo, $match);

        $id = (int) $match['id'];
        $this->generatorFileGuard->ensureIdIsUnique($id, $className, $fileInfo);

        return $id;
    }
}
