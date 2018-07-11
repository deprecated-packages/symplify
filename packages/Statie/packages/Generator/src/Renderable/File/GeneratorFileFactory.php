<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Renderable\File;

use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;
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

    public function __construct(PathAnalyzer $pathAnalyzer, GeneratorFileGuard $generatorFileGuard)
    {
        $this->pathAnalyzer = $pathAnalyzer;
        $this->generatorFileGuard = $generatorFileGuard;
    }

    /**
     * @param SplFileInfo[] $fileInfos
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

    private function createFromClassNameAndFileInfo(string $className, SplFileInfo $fileInfo): AbstractGeneratorFile
    {
        $id = $this->findAndGetValidId($fileInfo, $className);

        return new $className(
            $id,
            $fileInfo,
            $fileInfo->getRelativePathname(),
            $fileInfo->getPathname(),
            $this->pathAnalyzer->detectFilenameWithoutDate($fileInfo),
            $this->pathAnalyzer->detectDate($fileInfo)
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
