<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\LatteVariableCollector;

use Nette\Utils\Strings;
use PHPStan\Type\ObjectType;
use Symplify\LattePHPStanCompiler\Contract\LatteVariableCollectorInterface;
use Symplify\TemplatePHPStanCompiler\ValueObject\VariableAndType;

final class NonStaticFilterVariables implements LatteVariableCollectorInterface
{
    /**
     * @param array<string, string> $nonStaticFilters
     */
    public function __construct(
        private array $nonStaticFilters
    ) {
    }

    /**
     * @return VariableAndType[]
     */
    public function getVariablesAndTypes(): array
    {
        $variablesAndTypes = [];
        foreach ($this->nonStaticFilters as $nonStaticFilter) {
            [$className,] = explode('::', $nonStaticFilter, 2);
            $variableName = Strings::firstLower(Strings::replace($className, '/\\\\/', '')) . 'Filter';
            $variablesAndTypes[] = new VariableAndType($variableName, new ObjectType($className));
        }
        return $variablesAndTypes;
    }
}
