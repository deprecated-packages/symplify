<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ThisType;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreferredRawDataInTestDataProviderRule\PreferredRawDataInTestDataProviderRuleTest
 */
final class PreferredRawDataInTestDataProviderRule extends AbstractSymplifyRule
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

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
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

    private function findDataProviderClassMethod(ClassMethod $classMethod, string $methodName): ?ClassMethod
    {
        $class = $classMethod->getAttribute(PHPStanAttributeKey::PARENT);
        if (! $class instanceof Class_) {
            return null;
        }

        return $class->getMethod($methodName);
    }

    private function matchDataProviderMethodName(ClassMethod $classMethod): ?string
    {
        $docComment = $classMethod->getDocComment();
        if ($docComment === null) {
            return null;
        }

        $match = Strings::match($docComment->getText(), self::DATAPROVIDER_REGEX);
        if (! $match) {
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
