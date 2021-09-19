<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Source;

use PHPStan\Type\Type;

final class SomeValueObjectWrapper
{
    public function __construct(
        private Type|null $argumentType = null
    ) {
    }
}
