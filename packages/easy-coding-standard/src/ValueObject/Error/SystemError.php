<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ValueObject\Error;

use Symplify\EasyCodingStandard\Parallel\Contract\Serializable;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Name;

final class SystemError implements Serializable
{
    public function __construct(
        private int $line,
        private string $message,
        private string $relativeFilePath
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getFileWithLine(): string
    {
        return $this->relativeFilePath . ':' . $this->line;
    }

    /**
     * @return array{Name::MESSAGE: string, Name::RELATIVE_FILE_PATH: string, Name::LINE: int}
     */
    public function jsonSerialize(): array
    {
        return [
            Name::MESSAGE => $this->message,
            Name::RELATIVE_FILE_PATH => $this->relativeFilePath,
            Name::LINE => $this->line,
        ];
    }

    /**
     * @param array{Name::MESSAGE: $json string, Name::RELATIVE_FILE_PATH: string, Name::LINE: int}
     */
    public static function decode(array $json): self
    {
        return new self($json[Name::MESSAGE], $json[Name::RELATIVE_FILE_PATH], $json[Name::LINE],);
    }
}
