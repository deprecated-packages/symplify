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
    /**
     * @var string[]
     */
    private array $nativeMacrosNames = [];

    public function __construct(
        private PrivatesAccessor $privatesAccessor,
    ) {
        // make sure basic macros are installed
        CoreMacros::install($this);
        BlockMacros::install($this);
        UIMacros::install($this);

        $runtimeDefaults = new Defaults();
        $functionNames = array_keys($runtimeDefaults->getFunctions());
        $this->setFunctions($functionNames);

        $macros = $this->privatesAccessor->getPrivateProperty($this, 'macros');
        $this->nativeMacrosNames = array_keys($macros);
    }

    public function expandMacro(string $name, string $args, string $modifiers = '', string $nPrefix = null): MacroNode
    {
        // missing macro!
        if (! $this->isMacroRegistered($name)) {
            $this->fakeMacro($name);
        }

        return parent::expandMacro($name, $args, $modifiers, $nPrefix);
    }

    /**
     * Generates code for macro <tag n:attr> to the output.
     *
     * @internal
     */
    public function writeAttrsMacro(string $html): void
    {
        $htmlNode = $this->privatesAccessor->getPrivateProperty($this, 'htmlNode');

        // all collected n:attributes with nodes
        $attrs = $htmlNode->macroAttrs;

        foreach ($attrs as $macroName => $macroContent) {
            $this->fakeAttrMacro($macroName);
        }

        parent::writeAttrsMacro($html);
    }

    private function fakeMacro(string $name): void
    {
        $fakeMacroSet = new MacroSet($this);

        $fakeMacroSet->addMacro(
            $name,
            fn (MacroNode $macroNode, PhpWriter $phpWriter): string => $this->dummyMacro($macroNode, $phpWriter)
        );
    }

    private function fakeAttrMacro(string $name): void
    {
        // avoid override native n:macro
        if (in_array($name, $this->nativeMacrosNames, true)) {
            return;
        }

        $fakeMacroSet = new MacroSet($this);

        $fakeMacroSet->addMacro(
            $name,
            null,
            null,
            fn (MacroNode $macroNode, PhpWriter $phpWriter): string => $this->dummyAttrMacro($macroNode, $phpWriter),
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

    private function dummyAttrMacro(MacroNode $macroNode, PhpWriter $phpWriter): string
    {
        // nothing to render
        if ($macroNode->args === '') {
            return '';
        }

        // show parameters to allow php-parser to discover those variables
        return $phpWriter->write('echo %node.array');
    }

    private function isMacroRegistered(string $name): bool
    {
        return in_array($name, $this->nativeMacrosNames, true);
    }
}
