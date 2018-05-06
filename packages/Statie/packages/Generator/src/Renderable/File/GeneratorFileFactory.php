<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Renderable\File;

use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\Statie\Generator\Exception\Configuration\GeneratorException;
use Symplify\Statie\Utils\PathAnalyzer;

final class GeneratorFileFactory
{
    /**
     * @var PathAnalyzer
     */
    private $pathAnalyzer;

    /**
     * @var int[][]
     */
    private $idsByAbstractGeneratorFileClass = [];

    public function __construct(PathAnalyzer $pathAnalyzer)
    {
        $this->pathAnalyzer = $pathAnalyzer;
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return AbstractGeneratorFile[]
     */
    public function createFromFileInfosAndClass(array $fileInfos, string $class): array
    {
        $objects = [];

        $this->ensureIsAbstractGeneratorFile($class);

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
        $this->ensureIdIsSet($fileInfo, $match);

        $id = (int) $match['id'];

        $this->ensureIdIsUnique($id, $className, $fileInfo);
        $this->idsByAbstractGeneratorFileClass[$className][] = $id;

        return new $className(
            $id,
            $fileInfo,
            $fileInfo->getRelativePathname(),
            $fileInfo->getPathname(),
            $filenameWithoutDate,
            $dateTime
        );
    }

    private function ensureIsAbstractGeneratorFile(string $class): void
    {
        if (is_a($class, AbstractGeneratorFile::class, true)) {
            return;
        }

        throw new GeneratorException(sprintf('"%s" must inherit from "%s"', $class, AbstractGeneratorFile::class));
    }

    private function ensureIdIsUnique(int $id, string $className, SplFileInfo $fileInfo): void
    {
        if (! isset($this->idsByAbstractGeneratorFileClass[$className])) {
            return;
        }

        if (! in_array($id, $this->idsByAbstractGeneratorFileClass[$className], true)) {
            return;
        }

        throw new GeneratorException(sprintf(
            'Id "%d" was already set for "%s" class. Pick an another one for "%s" file.',
            $id,
            $className,
            $fileInfo->getRealPath()
        ));
    }

    /**
     * @param mixed[] $match
     */
    private function ensureIdIsSet(SplFileInfo $fileInfo, array $match): void
    {
        if (isset($match['id'])) {
            return;
        }

        throw new GeneratorException(sprintf(
            'File "%s" must have "id: [0-9]+" in the header in --- blocks.',
            $fileInfo->getRealPath()
        ));
    }
}
