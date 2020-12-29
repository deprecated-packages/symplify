<?php

declare(strict_types=1);

namespace Symplify\Astral\ValueObject\NodeBuilder;

use PhpParser\Builder\Namespace_;

/**
 * Fixed duplicated naming in php-parser and prevents confusion
 */
final class NamespaceBuilder extends Namespace_
{
}
