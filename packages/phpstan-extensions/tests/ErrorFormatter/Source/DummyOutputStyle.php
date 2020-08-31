<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\ErrorFormatter\Source;

use PHPStan\Command\OutputStyle;

final class DummyOutputStyle implements OutputStyle
{
    public function __construct()
    {
    }

    public function title(string $message): void
    {

    }

    public function section(string $message): void
    {

    }

    public function listing(array $elements): void
    {

    }

    public function success(string $message): void
    {

    }

    public function error(string $message): void
    {

    }

    public function warning(string $message): void
    {

    }

    public function note(string $message): void
    {

    }

    public function caution(string $message): void
    {

    }

    public function table(array $headers, array $rows): void
    {

    }

    public function newLine(int $count = 1): void
    {

    }

    public function progressStart(int $max = 0): void
    {

    }

    public function progressAdvance(int $step = 1): void
    {

    }

    public function progressFinish(): void
    {

    }
}
