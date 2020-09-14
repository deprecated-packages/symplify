<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Type\TypeWithClassName;
use Symfony\Component\Process\Process;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule\NoNewOutsideFactoryRuleTest
 */
final class NoNewOutsideFactoryRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use decouled factory service to create "%s" object';

    /**
     * @var string[]
     */
    private const ALLOWED_CLASSES = ['*FileInfo'];

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @var TypeWithClassName|null
     */
    private $typeWithClassName;

    public function __construct(ArrayStringAndFnMatcher $arrayStringAndFnMatcher)
    {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [New_::class, Return_::class];
    }

    /**
     * @param New_|Return_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        // just collect new type node here, so we have context later
        if ($node instanceof New_) {
            $newClassType = $scope->getType($node);
            if (! $newClassType instanceof TypeWithClassName) {
                return [];
            }

            $this->typeWithClassName = $newClassType;
            return [];
        }

        // working with return here
        if ($this->typeWithClassName === null) {
            return [];
        }

        // is new class allowed without factory or in right place?
        $newClassName = $this->typeWithClassName->getClassName();
        if ($this->arrayStringAndFnMatcher->isMatch($newClassName, self::ALLOWED_CLASSES)) {
            return [];
        }

        if ($this->isLocatedInCorrectlyNamedClass($scope)) {
            return [];
        }

        if ($node->expr === null) {
            $this->typeWithClassName = null;
            return [];
        }

        $returnType = $scope->getType($node->expr);

        // not a match, probably somewhere else
        if (! $this->typeWithClassName->equals($returnType)) {
            $this->typeWithClassName = null;
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $newClassName);
        return [$errorMessage];
    }

    private function isLocatedInCorrectlyNamedClass(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return true;
        }

        $currentClassName = $classReflection->getName();
        if (Strings::endsWith($currentClassName, 'Factory')) {
            return true;
        }

        return Strings::endsWith($currentClassName, 'Test');
    }
}
