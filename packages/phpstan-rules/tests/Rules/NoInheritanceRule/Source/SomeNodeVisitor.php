<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoInheritanceRule\Source;

use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;

abstract class SomeNodeVisitor extends NodeVisitorAbstract implements NodeVisitor
{

}
