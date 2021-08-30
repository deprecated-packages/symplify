<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Latte;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\BlockMacros;
use Latte\Macros\CoreMacros;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Latte\Runtime\Defaults;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

final class UnknownMacroAwareLatteCompiler extends Compiler
{
    public function __construct()
    {
        // make sure basic macros are installed
        CoreMacros::install($this);
        BlockMacros::install($this);
        UIMacros::install($this);

        $runtimeDefaults = new Defaults();
        $functionNames = array_keys($runtimeDefaults->getFunctions());
        $this->setFunctions($functionNames);
    }

    public function expandMacro(string $name, string $args, string $modifiers = '', string $nPrefix = null): MacroNode
    {
        $privatesAccessor = new PrivatesAccessor();
        $macros = $privatesAccessor->getPrivateProperty($this, 'macros');

        // missing macro!
        if (! isset($macros[$name])) {
            $this->fakeMacro($name);
        }

        return parent::expandMacro($name, $args, $modifiers, $nPrefix);
    }

    private function fakeMacro(string $name): void
    {
        // fake it :)
        $fakeMacroSet = new MacroSet($this);
        // renger args at least
        $fakeMacroSet->addMacro(
            $name,
            fn (MacroNode $macroNode, PhpWriter $phpWriter): string => $this->dummyMacro($macroNode, $phpWriter)
        );
    }

    private function dummyMacro(MacroNode $macroNode, PhpWriter $phpWriter): string
    {
        // nothing to render
        if ($macroNode->args === '') {
            return '';
        }

        // show parameters to allow php-parser to discover those variables
        return $phpWriter->write('echo %node.args;');
    }
}
