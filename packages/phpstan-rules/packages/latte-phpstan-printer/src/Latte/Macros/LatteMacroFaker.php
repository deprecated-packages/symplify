<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter\Latte\Macros;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

final class LatteMacroFaker
{
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

        $variablesString = $this->resolveMacroArgsToVariableOnlyString($macroNode, ' ');

        // no variables?
        if ($variablesString === '') {
            return '';
        }

        // render only variables, so php-parser can pick them up as used
        return $phpWriter->write('echo \'' . $macroNode->name . '="\' . ' . $variablesString . ' . \' " \'');
    }

    private function dummyMacro(MacroNode $macroNode, PhpWriter $phpWriter): string
    {
        $variablesString = $this->resolveMacroArgsToVariableOnlyString($macroNode, ',');

        // nothing to render
        if ($variablesString === '') {
            return '';
        }

        // show parameters to allow php-parser to discover those variables
        return $phpWriter->write('echo ' . $variablesString . ';');
    }

    /**
     * @param non-empty-string $separator
     */
    private function resolveMacroArgsToVariableOnlyString(MacroNode $macroNode, string $separator): string
    {
        $argumentsArray = explode($separator, $macroNode->args);

        // remove empty values
        $argumentsArray = array_values($argumentsArray);

        // keep only variables
        $variablesArray = array_filter($argumentsArray, fn (string $value): bool => str_starts_with($value, '$'));

        // clean variables from everything that can break PHP
        foreach ($variablesArray as $key => $variable) {
            $variable = trim($variable, ',');
            $variablesArray[$key] = $variable;
        }

        return implode($separator, $variablesArray);
    }
}
