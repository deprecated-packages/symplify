<?php

namespace Symplify\PHP7_CodeSniffer\Tests\DI\ExtensionHelperTraitSource;

use Nette\DI\CompilerExtension;
use Symplify\PHP7_CodeSniffer\DI\ExtensionHelperTrait;

final class ExtensionWithTrait extends CompilerExtension
{
    use ExtensionHelperTrait;
}
