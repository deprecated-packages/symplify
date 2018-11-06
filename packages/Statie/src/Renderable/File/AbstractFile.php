<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\File;

use DateTimeInterface;
use Nette\Utils\FileSystem;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

abstract class AbstractFile
{
    /**
     * @var mixed[]
     */
    protected $configuration = [];

    /**
     * @var SmartFileInfo
     */
    protected $fileInfo;

    /**
     * @var string
     */
    private $relativeSource;

    /**
     * @var string
     */
    private $outputPath;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $filenameWithoutDate;

    /**
     * @var DateTimeInterface|null
     */
    private $dateTime;

    public function __construct(
        SmartFileInfo $smartFileInfo,
        string $relativeSource,
        string $filePath,
        string $filenameWithoutDate,
        ?DateTimeInterface $dateTime
    ) {
        $this->relativeSource = $relativeSource;
        $this->fileInfo = $smartFileInfo;

        $this->filePath = $filePath;
        $this->content = FileSystem::read($smartFileInfo->getRealPath());

        // optional values
        $this->dateTime = $dateTime;
        $this->filenameWithoutDate = $filenameWithoutDate;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setOutputPath(string $outputPath): void
    {
        $this->outputPath = $outputPath;
    }

    public function getOutputPath(): string
    {
        return $this->outputPath;
    }

    public function setRelativeUrl(string $relativeUrl): void
    {
        $this->configuration['relativeUrl'] = $relativeUrl;
    }

    public function getRelativeUrl(): string
    {
        return $this->configuration['relativeUrl'];
    }

    public function getRelativeSource(): string
    {
        return $this->relativeSource;
    }

    public function getBaseName(): string
    {
        return $this->fileInfo->getBasename('.' . $this->fileInfo->getExtension());
    }

    public function getRelativeDirectory(): string
    {
        $directoryPathInfo = $this->fileInfo->getPathInfo();

        return $directoryPathInfo->getPathname();
    }

    public function getPrimaryExtension(): string
    {
        $fileParts = explode('.', $this->fileInfo->getBasename());
        if (count($fileParts) > 2) {
            return $fileParts[count($fileParts) - 2];
        }

        return $fileParts[count($fileParts) - 1];
    }

    public function getExtension(): string
    {
        return $this->fileInfo->getExtension();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function changeContent(string $newContent): void
    {
        $this->content = $newContent;
    }

    /**
     * @param mixed[] $configuration
     */
    public function addConfiguration(array $configuration): void
    {
        $this->configuration = array_merge($this->configuration, $configuration);
    }

    /**
     * @return mixed[]
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * Get single option from configuration
     *
     * @return mixed|null
     */
    public function getOption(string $name)
    {
        return $this->configuration[$name] ?? null;
    }

    public function getLayout(): ?string
    {
        return $this->configuration['layout'] ?? null;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getDateInFormat(string $format): ?string
    {
        if ($this->dateTime) {
            return $this->dateTime->format($format);
        }

        return null;
    }

    public function getFilenameWithoutDate(): string
    {
        return $this->filenameWithoutDate;
    }

    /**
     * @return int[]
     */
    public function getRelatedItemsIds(): array
    {
        return $this->getOption('related_items') ?? [];
    }
}
