<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\ErrorFormatter\Source;

use PHPStan\Command\Output;
use PHPStan\Command\OutputStyle;

final class DummyOutput implements Output
{
    /**
     * @var string
     */
    private $bufferedContent = '';

    public function writeFormatted(string $message): void
    {
        $this->bufferedContent .= $message . PHP_EOL;
    }

    public function writeLineFormatted(string $message): void
    {
        $this->bufferedContent .= $message . PHP_EOL;
    }

    public function writeRaw(string $message): void
    {
        $this->bufferedContent .= $message . PHP_EOL;
    }

    public function getStyle(): OutputStyle
    {
        return new DummyOutputStyle();
    }

    public function isVerbose(): bool
    {
        return false;
    }

    public function isDebug(): bool
    {
        return false;
    }

    public function getBufferedContent(): string
    {
        return $this->bufferedContent;
    }
}
