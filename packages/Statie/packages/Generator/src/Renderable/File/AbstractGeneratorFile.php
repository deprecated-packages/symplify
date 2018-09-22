<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Renderable\File;

use DateTimeInterface;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\Statie\Renderable\File\AbstractFile;

abstract class AbstractGeneratorFile extends AbstractFile
{
    /**
     * @var int
     */
    private $id;

    public function __construct(
        int $id,
        SmartFileInfo $smartFileInfo,
        string $relativeSource,
        string $filePath,
        string $filenameWithoutDate,
        ?DateTimeInterface $dateTime
    ) {
        $this->id = $id;
        parent::__construct($smartFileInfo, $relativeSource, $filePath, $filenameWithoutDate, $dateTime);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
