<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
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
    public const ERROR_MESSAGE = "Use raw data in test's dataProvider method instead from setUp()";

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
        $docComment = $node->getDocComment();
        if ($docComment === null) {
            return [];
        }

        if (! $match = Strings::match($docComment->getText(), self::DATAPROVIDER_REGEX)) {
            return [];
        }

        $dataProviderMethod = $match['dataProviderMethod'];
        $class = $node->getAttribute('parent');

        /** @var ClassMethod[] $classMethods */
        $classMethods = $this->nodeFinder->findInstanceOf($class, ClassMethod::class);
        foreach ($classMethods as $classMethod) {
            if ((string) $classMethod->name !== $dataProviderMethod) {
                continue;
            }

            if (! $this->isSkipped($classMethod, $scope)) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    private function isSkipped(ClassMethod $classMethod, Scope $scope): bool
    {
        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf((array) $classMethod->getStmts(), MethodCall::class);
        $skipped = false;
        foreach ($methodCalls as $methodCall) {
            $callerType = $scope->getType($methodCall->var);
            /** @var Identifier $name */
            $name = $methodCall->name;

            if ($callerType instanceof ThisType || strtolower((string) $name) === 'setup') {
                $skipped = true;
                break;
            }
        }

        return $skipped;
    }
}
