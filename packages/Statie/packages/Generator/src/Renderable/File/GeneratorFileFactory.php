<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Renderable\File;

use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\Statie\Utils\PathAnalyzer;

final class GeneratorFileFactory
{
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
        // @todo decouple to Filesystem tools
        $dateTime = $this->pathAnalyzer->detectDate($fileInfo);
        if ($dateTime) {
            $filenameWithoutDate = $this->pathAnalyzer->detectFilenameWithoutDate($fileInfo);
        } else {
            $filenameWithoutDate = $fileInfo->getBasename('.' . $fileInfo->getExtension());
        }

        $match = Strings::match($fileInfo->getContents(), '#id: (?<id>[0-9]+)#');
        $this->generatorFileGuard->ensureIdIsSet($fileInfo, $match);

        $id = (int) $match['id'];

        $this->generatorFileGuard->ensureIdIsUnique($id, $className, $fileInfo);

        return new $className(
            $id,
            $fileInfo,
            $fileInfo->getRelativePathname(),
            $fileInfo->getPathname(),
            $filenameWithoutDate,
            $dateTime
        );
    }
}
