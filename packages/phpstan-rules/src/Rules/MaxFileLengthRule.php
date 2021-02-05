<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Param;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\MaxFileLengthRule\MaxFileLengthRuleTest
 */
final class MaxFileLengthRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Paths for file "%s" has %d chars, but must be shorter than %d.';

    /**
     * @var int
     */
    private $maxLength;

    public function __construct(int $maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [FileNode::class];
    }

    /**
     * @param FileNode $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();
        $long = strlen($file);

        if ($long < $this->maxLength) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $file, $long, $this->maxLength)];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Path file must be shorten then configured maxLength', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
# file path
/app/foo/bar/baz.php
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
# file path
/app/foo/baz.php
CODE_SAMPLE
                ,
                [
                    'maxLength' => 17,
                ]
            ),
        ]);
    }
}
