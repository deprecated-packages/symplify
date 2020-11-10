<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;
use PhpParser\Node\Name\FullyQualified;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ExclusiveDependencyRule\ExclusiveDependencyRuleTest
 */
final class ExclusiveDependencyRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Only %s type can require %s type';

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @var array<string, string[]>
     */
    private $allowedTypeByLocations = [];

    /**
     * @param array<string, string[]> $allowedTypeByLocations
     */
    public function __construct(ArrayStringAndFnMatcher $arrayStringAndFnMatcher, array $allowedTypeByLocations = [])
    {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
        $this->allowedTypeByLocations = $allowedTypeByLocations;
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
        $methodName = (string) $node->name;
        if ($methodName !== '__construct') {
            return [];
        }

        $className = $this->getClassName($scope);
        if ($className === null) {
            return [];
        }

        $allowedTypes = [];
        foreach ($this->allowedTypeByLocations as $types) {
            foreach ($types as $type) {
                $allowedTypes[] = $type;
            }
        }

        // Use loop on purpose to fetch $location variable
        // to be re-used in next params check
        $foundInLocation = false;
        foreach (array_keys($this->allowedTypeByLocations) as $location) {
            if ($this->arrayStringAndFnMatcher->isMatch($className, [$location])) {
                $foundInLocation = true;
                break;
            }
        }

        $params = $node->getParams();

        foreach ($params as $param) {
            $type = $param->type;
            if (! $type instanceof FullyQualified) {
                continue;
            }

            $type = (string) $type;
            if (! in_array($type, $allowedTypes, true)) {
                continue;
            }

            if ($foundInLocation && in_array($type, $this->allowedTypeByLocations[$location], true)) {
                continue;
            }

            return [sprintf(self::ERROR_MESSAGE, $location, $type)];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        $description = sprintf(self::ERROR_MESSAGE, '"Type"', '"Dependency Type"');

        return new RuleDefinition($description, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeController
{
    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeRepository
{
    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
