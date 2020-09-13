<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPUnit\Framework\TestCase;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoMissingDirPathRule\NoMissingDirPathRuleTest
 */
final class NoMissingDirPathRule implements Rule
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

    public function getNodeType(): string
    {
        return Dir::class;
    }

    /**
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $parent = $node->getAttribute('parent');
        if (! $parent instanceof Concat) {
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

    private function isPartOfPHPUnit(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
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

        return $this->isBeingCheckedIfExists($concat);
    }

    private function isBeingCheckedIfExists(Concat $concat): bool
    {
        $parent = $concat->getAttribute('parent');
        if (! $parent instanceof Arg) {
            return false;
        }
        $parentParent = $parent->getAttribute('parent');
        if (! $parentParent instanceof FuncCall) {
            return false;
        }

        if ($parentParent->name instanceof Expr) {
            return false;
        }

        $funcCallName = (string) $parentParent->name;
        return in_array($funcCallName, ['is_file', 'file_exists', 'is_dir'], true);
    }
}
