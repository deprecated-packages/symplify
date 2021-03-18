<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoNewOutsideFactoryRule\NoNewOutsideFactoryRuleTest
 */
final class NoNewOutsideFactoryRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use decoupled factory service to create "%s" object';

    /**
     * @var string[]|class-string<Token>[]
     */
    private const ALLOWED_CLASSES = [
        '*FileInfo',
        '*\Node\*',
        Token::class,
        '*Reflection',
        'Reflection*',
        Node::class,
        Type::class,
    ];

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @var TypeWithClassName|null
     */
    private $typeWithClassName;

    public function __construct(ArrayStringAndFnMatcher $arrayStringAndFnMatcher)
    {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [New_::class, Return_::class];
    }

    /**
     * @param New_|Return_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        // just collect new type node here, so we have context later
        if ($node instanceof New_) {
            $newClassType = $scope->getType($node);
            if (! $newClassType instanceof TypeWithClassName) {
                return [];
            }

            $this->typeWithClassName = $newClassType;
            return [];
        }

        // working with return here
        if ($this->typeWithClassName === null) {
            return [];
        }

        // is new class allowed without factory or in right place?
        $newClassName = $this->typeWithClassName->getClassName();
        if ($this->arrayStringAndFnMatcher->isMatch($newClassName, self::ALLOWED_CLASSES)) {
            return [];
        }

        if ($this->isLocatedInCorrectlyNamedClass($scope)) {
            return [];
        }

        if ($node->expr === null) {
            $this->typeWithClassName = null;
            return [];
        }

        $returnType = $scope->getType($node->expr);

        // not a match, probably somewhere else
        if (! $this->typeWithClassName->equals($returnType)) {
            $this->typeWithClassName = null;
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $newClassName);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        return new SomeValueObject();
    }
}

CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeFactory
{
    public function create()
    {
        return new SomeValueObject();
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isLocatedInCorrectlyNamedClass(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return true;
        }

        $currentClassName = $classReflection->getName();
        if (Strings::endsWith($currentClassName, 'Factory')) {
            return true;
        }

        return Strings::endsWith($currentClassName, 'Test');
    }
}
