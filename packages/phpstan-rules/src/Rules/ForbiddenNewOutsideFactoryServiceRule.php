<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenNewOutsideFactoryServiceRule\ForbiddenNewOutsideFactoryServiceRuleTest
 */
final class ForbiddenNewOutsideFactoryServiceRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"new" outside factory is not allowed for object type "%s"';

    /**
     * @var array<string, string>
     */
    private $types = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param array<string, string> $types
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $types = [])
    {
        $this->types = $types;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    /**
     * @param New_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return [];
        }

        if (Strings::endsWith($className, 'Factory')) {
            return [];
        }

        foreach ($this->types as $type) {
            if (! $this->hasNewWithTypeInside($node, $type)) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $type);
            return [$errorMessage];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function process()
    {
        $anotherObject = new AnotherObject();
        // ...
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function __construt(AnotherObjectFactory $anotherObjectFactory)
    {
        $this->anotherObjectFactory = $anotherObjectFactory;
    }

    public function process()
    {
        $anotherObject = $this->anotherObjectFactory = $anotherObjectFactory->create();
        // ...
    }
}
CODE_SAMPLE
                ,
                [
                    'types' => ['AnotherObject'],
                ]
            ),
        ]);
    }

    private function hasNewWithTypeInside(New_ $new, string $type): bool
    {
        /** @var FullyQualified $fullyQualifiedName */
        $fullyQualifiedName = $new->class;
        if (! $fullyQualifiedName instanceof FullyQualified) {
            return false;
        }

        $className = (string) end($fullyQualifiedName->parts);
        if (! Strings::startsWith($type, '*')) {
            return $className === $type;
        }

        return Strings::match($className, '#.' . $type . '#') > 0;
    }
}
