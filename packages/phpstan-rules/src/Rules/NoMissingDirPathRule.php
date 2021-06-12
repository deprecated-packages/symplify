<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPUnit\Framework\TestCase;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\PHPStanRules\PhpParser\FileExistFuncCallAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoMissingDirPathRule\NoMissingDirPathRuleTest
 */
final class NoMissingDirPathRule extends AbstractSymplifyRule
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

    public function __construct(
        private FileExistFuncCallAnalyzer $fileExistFuncCallAnalyzer
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Dir::class];
    }

    /**
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $parent = $node->getAttribute(AttributeKey::PARENT);
        if (! $parent instanceof Concat) {
            return [];
        }

        $parentParent = $parent->getAttribute(AttributeKey::PARENT);
        if ($parentParent instanceof Concat) {
            return [];
        }

        if (! $parent->right instanceof String_) {
            return [];
        }

        $relativeDirPath = $parent->right->value;
        if ($this->shouldSkip($relativeDirPath, $parent, $scope)) {
            return [];
        }

        $realDirectory = dirname($scope->getFile());
        $fileRealPath = $realDirectory . $relativeDirPath;

        if (file_exists($fileRealPath)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $relativeDirPath);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return __DIR__ . '/missing_location.txt';
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return __DIR__ . '/existing_location.txt';
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isPartOfPHPUnit(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        $className = $classReflection->getName();
        return is_a($className, TestCase::class, true);
    }

    private function shouldSkip(string $relativeDirPath, Concat $concat, Scope $scope): bool
    {
        // is vendor autolaod? it yet to be exist
        if (Strings::match($relativeDirPath, self::VENDOR_REGEX)) {
            return true;
        }

        if (Strings::contains($relativeDirPath, '*')) {
            return true;
        }

        if ($this->isPartOfPHPUnit($scope)) {
            return true;
        }

        if ($this->fileExistFuncCallAnalyzer->isBeingCheckedIfExists($concat)) {
            return true;
        }

        if (Strings::match($relativeDirPath, self::BRACKET_PATH_REGEX)) {
            return true;
        }

        return $this->fileExistFuncCallAnalyzer->hasParentIfWithFileExistCheck($concat);
    }
}
