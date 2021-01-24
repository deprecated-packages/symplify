<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\PhpDoc\BarePhpDocParser;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ValidNetteInjectRule\ValidNetteInjectRuleTest
 */
final class ValidNetteInjectRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Nette @inject annotation must be valid';

    /**
     * @var string
     */
    private const INJECT_ANNOTATION = '@inject';

    /**
     * @var BarePhpDocParser
     */
    private $barePhpDocParser;

    public function __construct(BarePhpDocParser $barePhpDocParser)
    {
        $this->barePhpDocParser = $barePhpDocParser;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class, ClassMethod::class];
    }

    /**
     * @param Property|ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $phpDocTagNodes = $this->barePhpDocParser->parseNodeToPhpDocTagNodes($node);
        if ($phpDocTagNodes === []) {
            return [];
        }

        foreach ($phpDocTagNodes as $phpDocTagNode) {
            if (! Strings::startsWith($phpDocTagNode->name, self::INJECT_ANNOTATION)) {
                continue;
            }

            if (! $node->isPublic()) {
                return [self::ERROR_MESSAGE];
            }

            if ($phpDocTagNode->name === self::INJECT_ANNOTATION) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @injected
     * @var
     */
    public $someDependency;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @inject
     * @var
     */
    public $someDependency;
}
CODE_SAMPLE
            ),
        ]);
    }
}
