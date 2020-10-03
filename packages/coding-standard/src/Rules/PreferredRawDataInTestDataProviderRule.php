<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ThisType;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\PreferredRawDataInTestDataProviderRule\PreferredRawDataInTestDataProviderRuleTest
 */
final class PreferredRawDataInTestDataProviderRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Code configured at setUp() cannot be used in data provider. Move it to test() method';

    /**
     * @var string
     * @see https://regex101.com/r/WaNbZ1/2
     */
    private const DATAPROVIDER_REGEX = '#\*\s+@dataProvider\s+(?<dataProviderMethod>.*)\n?#';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $dataProviderMethodName = $this->matchDataProviderMethodName($node);
        if ($dataProviderMethodName === null) {
            return [];
        }

        $classMethod = $this->findDataProviderClassMethod($node, $dataProviderMethodName);
        if ($classMethod === null) {
            return [];
        }

        if ($this->isSkipped($classMethod, $scope)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function findDataProviderClassMethod(ClassMethod $classMethod, string $dataProviderMethod): ?ClassMethod
    {
        $class = $classMethod->getAttribute('parent');

        /** @var ClassMethod[] $classMethods */
        $classMethods = $this->nodeFinder->findInstanceOf($class, ClassMethod::class);
        foreach ($classMethods as $classMethod) {
            if ((string) $classMethod->name !== $dataProviderMethod) {
                continue;
            }

            return $classMethod;
        }

        return null;
    }

    private function matchDataProviderMethodName(ClassMethod $classMethod): ?string
    {
        $docComment = $classMethod->getDocComment();
        if ($docComment === null) {
            return null;
        }

        if (! $match = Strings::match($docComment->getText(), self::DATAPROVIDER_REGEX)) {
            return null;
        }

        return $match['dataProviderMethod'];
    }

    private function isSkipped(ClassMethod $classMethod, Scope $scope): bool
    {
        /** @var Variable[] $variables */
        $variables = $this->nodeFinder->findInstanceOf((array) $classMethod->getStmts(), Variable::class);
        foreach ($variables as $variable) {
            $callerType = $scope->getType($variable);
            if ($callerType instanceof ThisType) {
                return false;
            }
        }

        return true;
    }
}
