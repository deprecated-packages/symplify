<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\File;

use DateTimeInterface;
use Symfony\Component\Finder\SplFileInfo;

abstract class AbstractGeneratorFile extends AbstractFile
{
    /**
     * @var int
     */
    private $id;

    public function __construct(
        int $id,
        SplFileInfo $fileInfo,
        string $relativeSource,
        string $filePath,
        string $filenameWithoutDate,
        ?DateTimeInterface $dateTime
    ) {
        $this->id = $id;
        parent::__construct($fileInfo, $relativeSource, $filePath, $filenameWithoutDate, $dateTime);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
