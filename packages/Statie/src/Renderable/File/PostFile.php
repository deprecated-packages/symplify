<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\File;

use ArrayAccess;
use DateTimeInterface;
use Nette\Utils\FileSystem;
use Nette\Utils\ObjectHelpers;
use Nette\Utils\Strings;
use Symplify\Statie\Exception\Renderable\File\AccessKeyNotAvailableException;
use Symplify\Statie\Exception\Renderable\File\UnsupportedMethodException;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use function Safe\sprintf;

final class PostFile extends AbstractGeneratorFile implements ArrayAccess
{
    /**
     * @var int
     */
    private const READ_WORDS_PER_MINUTE = 260;

    public function getReadingTimeInMinutes(): int
    {
        $rawContent = FileSystem::read($this->fileInfo->getRealPath());
        $wordCount = substr_count($rawContent, ' ') + 1;

        return (int) ceil($wordCount / self::READ_WORDS_PER_MINUTE);
    }

    /**
     * @param mixed $offset
     * @return DateTimeInterface|string|null
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

    public function hasCode(): bool
    {
        $rawContent = FileSystem::read($this->fileInfo->getRealPath());

        $matches = Strings::matchAll($rawContent, '#\`\`\`#');

        return count($matches) > 4;
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

    /**
     * @param mixed $offset
     */
    private function ensureAccessExistingKey($offset): void
    {
        if (isset($this->configuration[$offset])) {
            return;
        }

        $availableKeys = array_keys($this->configuration);
        $suggestion = ObjectHelpers::getSuggestion($availableKeys, $offset);

        if ($suggestion) {
            $help = sprintf('Did you mean "%s"?', $suggestion);
        } else {
            $help = sprintf('Available keys are: "%s".', implode('", "', $availableKeys));
        }

        throw new AccessKeyNotAvailableException(sprintf(
            'Value "%s" was not found for "%s" object. %s',
            $offset,
            self::class,
            $help
        ));
    }
}
