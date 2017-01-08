<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\File;

use ArrayAccess;
use DateTimeInterface;
use Exception;
use SplFileInfo;
use Symplify\Statie\Utils\PathAnalyzer;

final class PostFile extends AbstractFile implements ArrayAccess
{
    /**
     * @var int
     */
    private const READ_WORDS_PER_MINUTE = 260;

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

    public function __construct(SplFileInfo $fileInfo, string $relativeSource)
    {
        parent::__construct($fileInfo, $relativeSource);

        $this->ensurePathStartsWithDate($fileInfo);

        $this->date = PathAnalyzer::detectDate($fileInfo);
        $this->filenameWithoutDate = PathAnalyzer::detectFilenameWithoutDate($fileInfo);

        $rawContent = strip_tags(file_get_contents($fileInfo->getRealPath()));
        $this->wordCount = count(explode(' ', $rawContent));
    }

    public function getDate() : DateTimeInterface
    {
        return $this->date;
    }

    public function getDateInFormat(string $format) : string
    {
        return $this->date->format($format);
    }

    public function getFilenameWithoutDate() : string
    {
        return $this->filenameWithoutDate;
    }

    public function getWordCount() : int
    {
        return $this->wordCount;
    }

    public function getReadingTimeInMinutes() : int
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

        if (! isset($this->configuration[$offset])) {
            throw new \Exception(sprintf(
                'Value "%s" was not found for "%s" object. Available values are "%s"',
                $offset,
                get_class(),
                implode('", "', array_keys($this->configuration))
            ));
        }

        return $this->configuration[$offset];
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->configuration[$offset]);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) : void
    {
        throw new Exception(__METHOD__ . ' is not supported');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset) : void
    {
        throw new Exception(__METHOD__ . ' is not supported');
    }

    private function ensurePathStartsWithDate(SplFileInfo $fileInfo) : void
    {
        if (! PathAnalyzer::startsWithDate($fileInfo)) {
            throw new Exception(
                'Post name has to start with a date in "Y-m-d" format. E.g. "2016-01-01-name.md".'
            );
        }
    }
}
