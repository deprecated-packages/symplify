<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckRequiredAutowireAutoconfigurePublicInConfigServiceRule\CheckRequiredAutowireAutoconfigurePublicInConfigServiceRuleTest
 */
final class CheckRequiredAutowireAutoconfigurePublicInConfigServiceRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'autowire(), autoconfigure(), and public() are required in config service';

    /**
     * @var string[]
     */
    private const REQUIRED_METHODS = ['autowire', 'autoconfigure', 'public'];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $type = $scope->getType($node->var);
        if (! $type instanceof ObjectType) {
            return [];
        }

        $className = $type->getClassName();
        if (! is_a($className, ServicesConfigurator::class, true)) {
            return [];
        }

        if (! $this->simpleNameResolver->isName($node->name, 'defaults')) {
            return [];
        }

        $methodCallNames = $this->getMethodCallNames($node);
        foreach (self::REQUIRED_METHODS as $requireMethod) {
            if (! in_array($requireMethod, $methodCallNames, true)) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public();
};
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();
};
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function getMethodCallNames(MethodCall $methodCall): array
    {
        $methodCalls = [];
        while ($methodCall) {
            if ($methodCall instanceof MethodCall) {
                $methodCallName = $this->simpleNameResolver->getName($methodCall->name);
                if ($methodCallName === null) {
                    continue;
                }

                $methodCalls[] = $methodCallName;
            }

            $methodCall = $methodCall->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return $methodCalls;
    }
}
