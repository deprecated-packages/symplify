<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\File;

use ArrayAccess;
use DateTimeInterface;
use Nette\Utils\ObjectMixin;
use SplFileInfo;
use Symplify\Statie\Exception\Renderable\File\AccessKeyNotAvailableException;
use Symplify\Statie\Exception\Renderable\File\MissingDateInFileNameException;
use Symplify\Statie\Exception\Renderable\File\UnsupportedMethodException;
use Symplify\Statie\Utils\PathAnalyzer;

final class PostFile extends AbstractFile implements ArrayAccess
{
    /**
     * @var int
     */
    private const READ_WORDS_PER_MINUTE = 260;

    /**
     * @var string
     */
    private const RELATED_POSTS = 'related_posts';

    /**
     * @var DateTimeInterface
     */
    private $date;

    /**
     * @var string
     */
    private $filenameWithoutDate;

    /**
     * @var int
     */
    private $wordCount;

    public function __construct(SplFileInfo $fileInfo, string $relativeSource, string $filePath)
    {
        parent::__construct($fileInfo, $relativeSource, $filePath);

        $this->ensurePathStartsWithDate($fileInfo);

        $this->date = PathAnalyzer::detectDate($fileInfo);
        $this->filenameWithoutDate = PathAnalyzer::detectFilenameWithoutDate($fileInfo);

        $rawContent = strip_tags(file_get_contents($fileInfo->getRealPath()));
        $this->wordCount = substr_count($rawContent, ' ') + 1;
    }

    public function getId(): ?int
    {
        return $this->configuration['id'] ?? null;
    }

    /**
     * @return int[]
     */
    public function getRelatedPostIds(): array
    {
        return $this->getConfiguration()[self::RELATED_POSTS] ?? [];
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getDateInFormat(string $format): string
    {
        return $this->date->format($format);
    }

    public function getFilenameWithoutDate(): string
    {
        return $this->filenameWithoutDate;
    }

    public function getWordCount(): int
    {
        return $this->wordCount;
    }

    public function getReadingTimeInMinutes(): int
    {
        return (int) ceil($this->wordCount / self::READ_WORDS_PER_MINUTE);
    }

    /**
     * @param mixed $offset
     *
     * @return DateTimeInterface|string
     */
    public function offsetGet($offset)
    {
        if ($offset === 'content') {
            return $this->getContent();
        }

        if ($offset === 'date') {
            return $this->getDate();
        }

        $this->ensureAccessExistingKey($offset);

        return $this->configuration[$offset];
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->configuration[$offset]);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new UnsupportedMethodException(__METHOD__ . ' is not supported');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new UnsupportedMethodException(__METHOD__ . ' is not supported');
    }

    private function ensurePathStartsWithDate(SplFileInfo $fileInfo): void
    {
        if (! PathAnalyzer::startsWithDate($fileInfo)) {
            throw new MissingDateInFileNameException(sprintf(
                'Post file "%s" name has to start with a date in "Y-m-d" format. E.g. "2016-01-01-name.md".',
                $fileInfo->getFilename()
            ));
        }
    }

    /**
     * @param mixed $offset
     */
    private function ensureAccessExistingKey($offset): void
    {
        if (! isset($this->configuration[$offset])) {
            $availableKeys = array_keys($this->configuration);
            $suggestion = ObjectMixin::getSuggestion($availableKeys, $offset);

            if ($suggestion) {
                $help = sprintf('Did you mean "%s"?', $suggestion);
            } else {
                $help = sprintf('Available keys are: "%s".', implode('", "', $availableKeys));
            }

            throw new AccessKeyNotAvailableException(sprintf(
                'Value "%s" was not found for "%s" object. %s',
                $offset,
                __CLASS__,
                $help
            ));
        }
    }
}
