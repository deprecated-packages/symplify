<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Latte;

use Latte\CompileException;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\BlockMacros;
use Latte\Macros\CoreMacros;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Latte\Runtime\Defaults;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Nette\Bridges\FormsLatte\FormMacros;
use Nette\Utils\Strings;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

final class UnknownMacroAwareLatteCompiler extends Compiler
{
    /**
     * @var string
     * @see https://regex101.com/r/bjXNkN/1
     */
    private const MISSING_MACRO_REGEX = '#^Unexpected {\/(?<macro_name>\w+)}#';

    /**
     * @var string[]
     */
    private array $nativeMacrosNames = [];

    /**
     * @var string[]
     */
    private array $endRequiringMacroNames = [];

    public function __construct(
        private PrivatesAccessor $privatesAccessor,
    ) {
        // make sure basic macros are installed
        CoreMacros::install($this);
        BlockMacros::install($this);

        if (class_exists('Nette\Bridges\ApplicationLatte\UIMacros')) {
            UIMacros::install($this);
        }

        if (class_exists('Nette\Bridges\FormsLatte\FormMacros')) {
            FormMacros::install($this);
        }

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
     * @param \Latte\Token[] $tokens
     */
    public function compile(array $tokens, string $className, string $comment = null, bool $strictMode = false): string
    {
        // @todo compile loop counter

        try {
            return parent::compile($tokens, $className, $className, $strictMode);
        } catch (CompileException $compileException) {
            // potential pair macro detection
            $match = Strings::match($compileException->getMessage(), self::MISSING_MACRO_REGEX);
            // nothing found, just fail
            if (! isset($match['macro_name'])) {
                throw $compileException;
            }

            // mark the dual macro tag and re-try
            $this->endRequiringMacroNames[] = $match['macro_name'];
            return $this->compile($tokens, $className, $comment, $strictMode);
        }
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

        if (in_array($name, $this->endRequiringMacroNames, true)) {
            $fakeMacroSet->addMacro(
                $name,
                fn (MacroNode $macroNode, PhpWriter $phpWriter): string => $this->dummyEndingMacro(
                    $macroNode,
                    $phpWriter
                ),
                // faking close macro
                fn (MacroNode $macroNode, PhpWriter $phpWriter): string => ''
            );
        } else {
            $fakeMacroSet->addMacro(
                $name,
                fn (MacroNode $macroNode, PhpWriter $phpWriter): string => $this->dummyMacro($macroNode, $phpWriter),
            );
        }
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

    private function dummyEndingMacro(MacroNode $macroNode, PhpWriter $phpWriter): string
    {
        // nothing to render
        if ($macroNode->args === '') {
            return '';
        }

        // show parameters to allow php-parser to discover those variables
        return $phpWriter->write('$temporary = %node.array;');
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
