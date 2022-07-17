<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\NodeAnalyzer\FileCheckingFuncCallAnalyzer;
use Symplify\PHPStanRules\NodeVisitor\FlatConcatFindingNodeVisitor;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoMissingDirPathRule\NoMissingDirPathRuleTest
 */
final class NoMissingDirPathRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The path "%s" was not found';

    /**
     * @see https://regex101.com/r/OzFMNQ/1
     * @var string
     */
    private const VENDOR_REGEX = '#(vendor|autoload\.php)#';

    /**
     * @see https://regex101.com/r/LS39sv/1
     * @var string
     */
    private const BRACKET_PATH_REGEX = '#\{(.*?)\}#';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        // test fixture can exist or not, better skip this case to avoid false positives
        if ($classReflection->isSubclassOf(TestCase::class)) {
            return [];
        }

        // mimics node finding visitors of NodeFinder with ability to stop traversing deeper
        $nodeTraverser = new NodeTraverser();
        $flatConcatFindingNodeVisitor = new FlatConcatFindingNodeVisitor(new FileCheckingFuncCallAnalyzer());

        $nodeTraverser->addVisitor($flatConcatFindingNodeVisitor);
        $nodeTraverser->traverse($classLike->stmts);

        $concats = $flatConcatFindingNodeVisitor->getFoundNodes();
        $errorMessages = [];

        foreach ($concats as $concat) {
            if (! $concat->left instanceof Dir) {
                return [];
            }

            if (! $concat->right instanceof String_) {
                return [];
            }

            $string = $concat->right;
            $relativeDirPath = $string->value;

            if ($this->shouldSkip($relativeDirPath)) {
                continue;
            }

            $realDirectory = dirname($scope->getFile());
            $fileRealPath = $realDirectory . $relativeDirPath;

            if (file_exists($fileRealPath)) {
                continue;
            }

            $errorMessages[] = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $relativeDirPath))
                ->line($concat->getLine())
                ->build();
        }

        return $errorMessages;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$filePath = __DIR__ . '/missing_location.txt';
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$filePath = __DIR__ . '/existing_location.txt';
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkip(string $relativeDirPath): bool
    {
        // is vendor autolaod? it yet to be exist
        if (Strings::match($relativeDirPath, self::VENDOR_REGEX)) {
            return true;
        }

        if (\str_contains($relativeDirPath, '*')) {
            return true;
        }

        $bracketMatches = Strings::match($relativeDirPath, self::BRACKET_PATH_REGEX);
        return $bracketMatches !== null;
    }
}
