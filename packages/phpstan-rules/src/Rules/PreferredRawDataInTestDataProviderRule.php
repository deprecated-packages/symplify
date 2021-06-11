<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ThisType;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

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
     * @return array<class-string<Node>>
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
        if (! $classMethod instanceof ClassMethod) {
            return [];
        }

        if ($this->isSkipped($classMethod, $scope)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class UseDataFromSetupInTestDataProviderTest extends TestCase
{
    private $data;

    protected function setUp()
    {
        $this->data = true;
    }

    public function provideFoo()
    {
        yield [$this->data];
    }

    /**
     * @dataProvider provideFoo
     */
    public function testFoo($value)
    {
        $this->assertTrue($value);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use stdClass;

final class UseRawDataForTestDataProviderTest
{
    private $obj;

    protected function setUp()
    {
        $this->obj = new stdClass;
    }

    public function provideFoo()
    {
        yield [true];
    }

    /**
     * @dataProvider provideFoo
     */
    public function testFoo($value)
    {
        $this->obj->x = $value;
        $this->assertTrue($this->obj->x);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function findDataProviderClassMethod(ClassMethod $classMethod, string $methodName): ?ClassMethod
    {
        $class = $classMethod->getAttribute(AttributeKey::PARENT);
        if (! $class instanceof Class_) {
            return null;
        }

        return $class->getMethod($methodName);
    }

    private function matchDataProviderMethodName(ClassMethod $classMethod): ?string
    {
        $docComment = $classMethod->getDocComment();
        if (! $docComment instanceof Doc) {
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
