<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\PHPStan\Types\ContainsTypeAnalyser;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\RequireNewArgumentConstantRule\RequireNewArgumentConstantRuleTest
 */
final class RequireNewArgumentConstantRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'New expression argument on position %d must use constant over value';

    /**
     * @var array<class-string, mixed[]>
     */
    private $constantArgByNewByType = [];

    /**
     * @var ContainsTypeAnalyser
     */
    private $containsTypeAnalyser;

    /**
     * @param array<class-string, mixed[]> $constantArgByNewByType
     */
    public function __construct(ContainsTypeAnalyser $containsTypeAnalyser, array $constantArgByNewByType = [])
    {
        $this->constantArgByNewByType = $constantArgByNewByType;
        $this->containsTypeAnalyser = $containsTypeAnalyser;
    }

    /**
     * @return string[]
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
        if (! $node->name instanceof Identifier) {
            return [];
        }

        $errorMessages = [];

        $methodName = (string) $node->name;

        foreach ($this->constantArgByNewByType as $type => $positionsByNews) {
            $positions = $this->matchPositions($node, $scope, $type, $positionsByNews, $methodName);
            if ($positions === null) {
                continue;
            }

            foreach ($node->args as $key => $arg) {
                if ($this->shouldSkipArg($key, $positions, $arg)) {
                    continue;
                }

                $errorMessages[] = sprintf(self::ERROR_MESSAGE, $key);
            }
        }

        return $errorMessages;
    }

    /**
     * @param class-string $desiredType
     * @return mixed|null
     */
    private function matchPositions(
        New_ $new,
        Scope $scope,
        string $desiredType,
        array $positionsByNews,
        string $methodName
    ) {
        if (! $this->containsTypeAnalyser->containsExprTypes($new->class, $scope, [$desiredType])) {
            return null;
        }

        return $positionsByNews[$methodName] ?? null;
    }

    /**
     * @param int[] $positions
     */
    private function shouldSkipArg(int $key, array $positions, Arg $arg): bool
    {
        if (! in_array($key, $positions, true)) {
            return true;
        }

        if ($arg->value instanceof Variable) {
            return true;
        }

        return $arg->value instanceof ClassConstFetch;
    }
}
