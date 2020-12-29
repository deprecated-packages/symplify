<?php

declare(strict_types=1);

namespace Symplify\Astral\ValueObject\NodeBuilder;

use PhpParser\Builder\TraitUse;

/**
 * Fixed duplicated naming in php-parser and prevents confusion
 */
final class TraitUseBuilder extends TraitUse
{
}
