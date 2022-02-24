<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\Latte\Macros;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Utils\Strings;

final class LatteMacroFaker
{
    /**
     * @var string
     * @see https://regex101.com/r/T10yro/1
     */
    private const VARIABLE_NAME_REGEX = '#(?<variable_name>\$[\w0-9\_]+)#';

    /**
     * @param string[] $endRequiringMacroNames
     */
    public function fakeMacro(Compiler $compiler, string $name, array $endRequiringMacroNames): void
    {
        $fakeMacroSet = new MacroSet($compiler);

        if (in_array($name, $endRequiringMacroNames, true)) {
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

    /**
     * @param string[] $nativeMacrosNames
     */
    public function fakeAttrMacro(Compiler $compiler, array $nativeMacrosNames, string $name): void
    {
        // avoid override native n:macro
        if (in_array($name, $nativeMacrosNames, true)) {
            return;
        }

        $fakeMacroSet = new MacroSet($compiler);

        $fakeMacroSet->addMacro(
            $name,
            null,
            null,
            fn (MacroNode $macroNode, PhpWriter $phpWriter): string => $this->dummyAttrMacro($macroNode, $phpWriter),
        );
    }

    public function dummyEndingMacro(MacroNode $macroNode, PhpWriter $phpWriter): string
    {
        // nothing to render
        if ($macroNode->args === '') {
            return '';
        }

        // show parameters to allow php-parser to discover those variables
        return $phpWriter->write('$temporary = %node.array;');
    }

    public function dummyAttrMacro(MacroNode $macroNode, PhpWriter $phpWriter): string
    {
        // nothing to render
        if ($macroNode->args === '') {
            return $macroNode->name;
        }

        // show parameters to allow php-parser to discover those variables
        // inspiration @see https://github.com/nette/latte/blob/7943f0693a7632ae41e844446f17035e1e3ddb52/src/Latte/Macros/CoreMacros.php#L557-L567

        $variablesString = $this->resolveMacroArgsToVariableOnlyString($macroNode);

        // no variables?
        if ($variablesString === '') {
            return '';
        }

        // render only variables, so php-parser can pick them up as used
        return $phpWriter->write('echo \'' . $macroNode->name . '="\' . ' . $variablesString . ' . \' " \'');
    }

    private function dummyMacro(MacroNode $macroNode, PhpWriter $phpWriter): string
    {
        $variablesString = $this->resolveMacroArgsToVariableOnlyString($macroNode);

        // nothing to render
        if ($variablesString === '') {
            return '';
        }

        // show parameters to allow php-parser to discover those variables
        return $phpWriter->write('echo ' . $variablesString . ';');
    }

    private function resolveMacroArgsToVariableOnlyString(MacroNode $macroNode): string
    {
        $variableMatches = Strings::matchAll($macroNode->args, self::VARIABLE_NAME_REGEX);

        $variableNames = [];
        foreach ($variableMatches as $variableMatch) {
            $variableNames[] = $variableMatch['variable_name'];
        }

        return implode(' . ', $variableNames);
    }
}
