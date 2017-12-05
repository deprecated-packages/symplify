<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\File;

use DateTimeInterface;
use SplFileInfo;
use Symplify\Statie\Utils\PathAnalyzer;

abstract class AbstractFile
{
    /**
     * @var SplFileInfo
     */
    protected $fileInfo;

    /**
     * @var mixed[]
     */
    protected $configuration = [];

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
     * @var DateTimeInterface|null
     */
    private $dateTime;

    /**
     * @var string
     */
    private $filenameWithoutDate;

    public function __construct(SplFileInfo $fileInfo, string $relativeSource, string $filePath)
    {
        $this->relativeSource = $relativeSource;
        $this->fileInfo = $fileInfo;
        $this->filePath = $filePath;
        $this->content = file_get_contents($fileInfo->getRealPath());

        // optional values
        $this->dateTime = PathAnalyzer::detectDate($fileInfo);
        if ($this->dateTime) {
            $this->filenameWithoutDate = PathAnalyzer::detectFilenameWithoutDate($fileInfo);
        } else {
            $this->filenameWithoutDate = $fileInfo->getBasename('.' . $fileInfo->getExtension());
        }
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
}
