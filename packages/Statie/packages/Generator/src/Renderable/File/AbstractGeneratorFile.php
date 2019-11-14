<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Renderable\File;

use DateTimeInterface;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\Statie\Renderable\File\AbstractFile;

abstract class AbstractGeneratorFile extends AbstractFile
{
    /**
     * @var int
     */
    private $id;

    /**
     * Content without configuratoin, without markdown, just text
     * @var string
     */
    private $rawContent;

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

    public function setRawContent(string $rawContent): void
    {
        $this->rawContent = $rawContent;
    }

    public function getRawContent(): string
    {
        return $this->rawContent;
    }
}
