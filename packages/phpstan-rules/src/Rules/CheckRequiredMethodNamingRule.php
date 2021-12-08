<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\NodeAnalyzer\AutowiredMethodPropertyAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodNamingRule\CheckRequiredMethodNamingRuleTest
 */
final class CheckRequiredMethodNamingRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Autowired/inject method name "%s()" must respect "autowire/inject(*)" name';

    /**
     * @var string[]
     */
    private const ALLOWED_METHOD_NAMES = ['autowire', 'inject'];

    public function __construct(
        private AutowiredMethodPropertyAnalyzer $autowiredMethodPropertyAnalyzer,
    ) {
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
        if (! $this->autowiredMethodPropertyAnalyzer->detect($node)) {
            return [];
        }

        $methodName = (string) $node->name;
        if ($this->hasRequiredName($methodName)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $methodName);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Contracts\Service\Attribute\Required;

final class SomeClass
{
    #[Required]
    public function install(...)
    {
        // ...
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Symfony\Contracts\Service\Attribute\Required;

final class SomeClass
{
    #[Required]
    public function autowire(...)
    {
        // ...
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function hasRequiredName(string $methodName): bool
    {
        foreach (self::ALLOWED_METHOD_NAMES as $allowedMethodName) {
            if ($methodName === $allowedMethodName) {
                return true;
            }

            if (str_starts_with($methodName, $allowedMethodName)) {
                return true;
            }
        }

        return false;
    }
}
