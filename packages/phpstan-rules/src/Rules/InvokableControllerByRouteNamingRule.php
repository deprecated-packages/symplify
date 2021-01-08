<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\ValueObject\MethodName;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\InvokableControllerByRouteNamingRule\InvokableControllerByRouteNamingRuleTest
 */
final class InvokableControllerByRouteNamingRule extends AbstractInvokableControllerRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use controller class name based on route name instead';

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
        if (! $this->isInControllerClass($scope)) {
            return [];
        }

        /** @var Identifier $classMethodIdentifier */
        $classMethodIdentifier = $node->name;
        $classMethodName = (string) $classMethodIdentifier;
        if ($classMethodName !== MethodName::INVOKE) {
            return [];
        }

        $fullyQualified = $this->getRouteAttribute($node);
        if ($fullyQualified === null) {
            return [];
        }

        /** @var Attribute|null $parent */
        $parent = $fullyQualified->getAttribute(PHPStanAttributeKey::PARENT);
        if (! $parent instanceof Attribute) {
            return [];
        }

        return $this->validateInvokable($scope, $parent->args);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Component\Routing\Annotation\Route;

final class SecurityController extends AbstractController
{
    #[Route(path: '/logout', name: 'logout')]
    public function __invoke(): Response
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Symfony\Component\Routing\Annotation\Route;

final class LogoutController extends AbstractController
{
    #[Route(path: '/logout', name: 'logout')]
    public function __invoke(): Response
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function validateInvokable(Scope $scope, array $array): array
    {
        foreach ($array as $arg) {
            /** @var Identifier $argIdentifier */
            $argIdentifier = $arg->name;
            $argName = (string) $argIdentifier;

            if ($argName === 'name') {
                $next = $argIdentifier->getAttribute(PHPStanAttributeKey::NEXT);
                if ($next instanceof String_) {
                    return $this->validateName($scope, $next->value);
                }
            }
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function validateName(Scope $scope, string $string): array
    {
        $shortClassName = $this->getShortClassName($scope);
        if ($shortClassName === null) {
            return [];
        }

        $name = Strings::endsWith($shortClassName, 'Controller')
            ? substr($shortClassName, 0, -10)
            : $shortClassName;

        if (strtolower($name) === strtolower($string)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
