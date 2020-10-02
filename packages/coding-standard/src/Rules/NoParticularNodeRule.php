<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoParticularNodeRule\NoParticularNodeRuleTest
 */
final class NoParticularNodeRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Node "%s" is fobidden to use';

    /**
     * @var string[]
     */
    private $forbiddenNodes = [];

    /**
     * @param string[] $forbiddenNodes
     */
    public function __construct(array $forbiddenNodes = [])
    {
        foreach ($forbiddenNodes as $forbiddenNode) {
            if (is_a($forbiddenNode, Node::class, true)) {
                continue;
            }

            $message = sprintf('"%s" must be child of "%s"', $forbiddenNode, Node::class);
            throw new ShouldNotHappenException($message);
        }

        $this->forbiddenNodes = $forbiddenNodes;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Node::class];
    }

    /**
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        foreach ($this->forbiddenNodes as $forbiddenNode) {
            if (! is_a($node, $forbiddenNode, true)) {
                continue;
            }

            $name = (string) Strings::after($forbiddenNode, '\\', -1);
            $name = rtrim($name, '_');
            $name = Strings::lower($name);

            $errorMessage = sprintf(self::ERROR_MESSAGE, $name);
            return [$errorMessage];
        }

        return [];
    }
}
