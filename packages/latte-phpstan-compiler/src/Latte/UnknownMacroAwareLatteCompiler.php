<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\Latte;

use Latte\CompileException;
use Latte\Compiler;
use Latte\HtmlNode;
use Latte\MacroNode;
use Latte\Macros\BlockMacros;
use Latte\Macros\CoreMacros;
use Latte\Runtime\Defaults;
use Latte\Token;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Nette\Bridges\FormsLatte\FormMacros;
use Nette\Utils\Strings;
use Symplify\LattePHPStanCompiler\Latte\Macros\LatteMacroFaker;
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
        private LatteMacroFaker $latteMacroFaker,
    ) {
        $this->installDefaultMacros($this);

        $runtimeDefaults = new Defaults();
        $functionNames = array_keys($runtimeDefaults->getFunctions());
        $this->setFunctions($functionNames);

        /** @var array<string, mixed> $macros */
        $macros = $this->privatesAccessor->getPrivateProperty($this, 'macros');

        $this->nativeMacrosNames = array_keys($macros);
        sort($this->nativeMacrosNames);
    }

    /**
     * @override
     */
    public function expandMacro(string $name, string $args, string $modifiers = '', string $nPrefix = null): MacroNode
    {
        // missing macro!
        if (! in_array($name, $this->nativeMacrosNames, true)) {
            $this->latteMacroFaker->fakeMacro($this, $name, $this->endRequiringMacroNames);
        }

        return parent::expandMacro($name, $args, $modifiers, $nPrefix);
    }

    /**
     * @param Token[] $tokens
     */
    public function compile(array $tokens, string $className, string $comment = null, bool $strictMode = true): string
    {
        // @todo compile loop counter?
        try {
            return parent::compile($tokens, $className, $className, $strictMode);
        } catch (CompileException $compileException) {
            // potential pair macro detection
            $match = Strings::match($compileException->getMessage(), self::MISSING_MACRO_REGEX);
            // nothing found, just fail
            if (! isset($match['macro_name'])) {
                throw $compileException;
            }

            // mark the dual macro tag and re-try compiling
            $this->endRequiringMacroNames[] = $match['macro_name'];

            return $this->compile($tokens, $className, $comment, $strictMode);
        }
    }

    /**
     * Generates code for macro <tag n:attr> to the output.
     *
     * @internal
     * @override
     */
    public function writeAttrsMacro(string $html, ?bool $empty = null): void
    {
        $htmlNode = $this->privatesAccessor->getPrivatePropertyOfClass($this, 'htmlNode', HtmlNode::class);

        // all collected n:attributes with nodes
        $attrs = $htmlNode->macroAttrs;

        foreach (array_keys($attrs) as $macroName) {
            $this->latteMacroFaker->fakeAttrMacro($this, $this->nativeMacrosNames, $macroName);
        }

        parent::writeAttrsMacro($html, $empty);
    }

    private function installDefaultMacros(self $compiler): void
    {
        // make sure basic macros are installed
        CoreMacros::install($compiler);
        BlockMacros::install($compiler);

        if (class_exists('Nette\Bridges\ApplicationLatte\UIMacros')) {
            UIMacros::install($compiler);
        }

        if (class_exists('Nette\Bridges\FormsLatte\FormMacros')) {
            FormMacros::install($compiler);
        }
    }
}
