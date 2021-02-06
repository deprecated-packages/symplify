<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\ValueObject;

final class Regex
{
    /**
     * @var string
     * @see https://regex101.com/r/tMVmLT/1
     */
    public const NEWLINE_REGEX = "#\r\n|\n|\r#";
}
