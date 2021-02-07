<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ExclusiveNamespaceRule\ExclusiveNamespaceRuleTest
 */
final class ExclusiveNamespaceRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Namespace "%s" is only reserved for "%s". Move the class somewhere else';

    /**
     * @var string
     * @see https://regex101.com/r/EWkKKs/1
     */
    private const EXCLUDED_SUFFIX_REGEX = '#(Test|Trait)$#';

    /**
     * @see https://regex101.com/r/HrTNSI/1
     * @var string
     */
    private const EXCLUDED_NAMESPACE_REGEX = '#\\\\(Exception|Contract)\\\\#';

    /**
     * @var string[]
     */
    private $namespaceParts = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param string[] $namespaceParts
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $namespaceParts = [])
    {
        $this->namespaceParts = $namespaceParts;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return class-string<Node>[]
     */
    public function getNodeTypes(): array
    {
        return [ClassLike::class];
    }

    /**
     * @param ClassLike $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classLikeName = $this->simpleNameResolver->getName($node);
        if ($classLikeName === null) {
            return [];
        }

        // skip interface and tests, except tests here
        if (Strings::match($classLikeName, self::EXCLUDED_NAMESPACE_REGEX)) {
            return [];
        }

        $namespace = $scope->getNamespace();
        if ($namespace === null) {
            return [];
        }

        foreach ($this->namespaceParts as $namespacePart) {
            if (! Strings::endsWith($namespace, $namespacePart)) {
                continue;
            }

            if (Strings::match($classLikeName, self::EXCLUDED_SUFFIX_REGEX)) {
                continue;
            }

            if (Strings::endsWith($classLikeName, $namespacePart)) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $namespace, $namespacePart);
            return [$errorMessage];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Exclusive namespace can only contain classes of specific type, nothing else', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
namespace App\Presenter;

class SomeRepository
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
namespace App\Presenter;

class SomePresenter
{
}
CODE_SAMPLE
                ,
                [
                    'namespaceParts' => ['Presenter'],
                ]
            ),
        ]);
    }
}
