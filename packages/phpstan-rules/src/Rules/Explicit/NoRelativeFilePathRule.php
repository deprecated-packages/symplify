<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeTraverser;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\NodeAnalyzer\FileCheckingFuncCallAnalyzer;
use Symplify\PHPStanRules\NodeVisitor\StringOutsideConcatFindingNodeVisitor;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoRelativeFilePathRule\NoRelativeFilePathRuleTest
 */
final class NoRelativeFilePathRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Relative file path "%s" is not allowed, use absolute one with __DIR__';

    /**
     * @var string
     * @see https://regex101.com/r/833ECx/1
     */
    private const URL_START_REGEX = '#^(https|http|git|ssh|ftp|file)://#';

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
        $strings = $this->findBareStrings($classLike);

        $errorMessages = [];

        foreach ($strings as $string) {
            // is it a file string?
            if (! $this->isFileString($string)) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $string->value);

            $errorMessages[] = RuleErrorBuilder::message($errorMessage)
                ->line($string->getLine())
                ->build();
        }

        return $errorMessages;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$filePath = 'some_file.txt';
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$filePath = __DIR__ . '/some_file.txt';
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return String_[]
     */
    private function findBareStrings(ClassLike $classLike): array
    {
        // mimics node finding visitors of NodeFinder with ability to stop traversing deeper
        $nodeTraverser = new NodeTraverser();

        $stringOutsideConcatFindingNodeVisitor = new StringOutsideConcatFindingNodeVisitor(
            new FileCheckingFuncCallAnalyzer()
        );

        $nodeTraverser->addVisitor($stringOutsideConcatFindingNodeVisitor);
        $nodeTraverser->traverse($classLike->stmts);

        return $stringOutsideConcatFindingNodeVisitor->getFoundNodes();
    }

    private function isFileString(String_ $string): bool
    {
        // probably an url
        if (Strings::match($string->value, self::URL_START_REGEX)) {
            return false;
        }

        // probably an email
        if (str_contains($string->value, '@')) {
            return false;
        }

        $pathInfo = pathinfo($string->value);
        if (! isset($pathInfo['extension'])) {
            return false;
        }

        $fileExtension = $pathInfo['extension'];

        if (strlen($fileExtension) > 3) {
            return false;
        }

        if (strlen($fileExtension) < 3) {
            return false;
        }

        if ($pathInfo['filename'] === '') {
            return false;
        }

        if (str_contains($pathInfo['filename'], '*')) {
            return false;
        }

        // only letters
        return ctype_alpha($fileExtension);
    }
}
