<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\PhpDoc\ResolvedPhpDocBlock;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\FileTypeMapper;
use PHPUnit\Framework\TestCase;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\SeeAnnotationToTestRule\SeeAnnotationToTestRuleTest
 */
final class SeeAnnotationToTestRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" is missing @see annotation with test case class reference';

    /**
     * @var FileTypeMapper
     */
    private $fileTypeMapper;

    /**
     * @var Broker
     */
    private $broker;

    /**
     * @var string[]
     */
    private $requiredSeeTypes = [];

    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param string[] $requiredSeeTypes
     */
    public function __construct(
        Broker $broker,
        SimpleNameResolver $simpleNameResolver,
        FileTypeMapper $fileTypeMapper,
        array $requiredSeeTypes = []
    ) {
        $this->fileTypeMapper = $fileTypeMapper;
        $this->broker = $broker;
        $this->requiredSeeTypes = $requiredSeeTypes;

        $this->privatesAccessor = new PrivatesAccessor();
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classReflection = $this->matchClassReflection($node);
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if ($this->shouldSkipClassReflection($classReflection)) {
            return [];
        }

        $docComment = $node->getDocComment();
        $errorMessage = sprintf(self::ERROR_MESSAGE, $classReflection->getName());
        if (! $docComment instanceof Doc) {
            return [$errorMessage];
        }

        $resolvedPhpDocBlock = $this->resolvePhpDoc($scope, $classReflection, $docComment);

        // skip deprectaed
        $deprecatedTags = $resolvedPhpDocBlock->getDeprecatedTag();
        if ($deprecatedTags !== null) {
            return [];
        }

        $seeTags = $this->getSeeTagNodes($resolvedPhpDocBlock);
        if ($this->hasSeeTestCaseAnnotation($seeTags)) {
            return [];
        }

        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass extends Rule
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
/**
 * @see SomeClassTest
 */
class SomeClass extends Rule
{
}
CODE_SAMPLE
                ,
                [
                    'requiredSeeTypes' => ['Rule'],
                ]
            ),
        ]);
    }

    private function shouldSkipClassReflection(ClassReflection $classReflection): bool
    {
        if ($classReflection->isAbstract()) {
            return true;
        }

        foreach ($this->requiredSeeTypes as $requiredSeeType) {
            if ($classReflection->isSubclassOf($requiredSeeType)) {
                return false;
            }
        }

        return true;
    }

    private function matchClassReflection(Class_ $class): ?ClassReflection
    {
        $className = $this->simpleNameResolver->getName($class);
        if ($className === null) {
            return null;
        }

        if (! class_exists($className)) {
            return null;
        }

        return $this->broker->getClass($className);
    }

    private function resolvePhpDoc(Scope $scope, ClassReflection $classReflection, Doc $doc): ResolvedPhpDocBlock
    {
        return $this->fileTypeMapper->getResolvedPhpDoc(
            $scope->getFile(),
            $classReflection->getName(),
            null,
            null,
            $doc->getText()
        );
    }

    /**
     * @param PhpDocTagNode[] $seeTags
     */
    private function hasSeeTestCaseAnnotation(array $seeTags): bool
    {
        foreach ($seeTags as $seeTag) {
            if (! $seeTag->value instanceof GenericTagValueNode) {
                continue;
            }

            if (is_a($seeTag->value->value, TestCase::class, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return PhpDocTagNode[]
     */
    private function getSeeTagNodes(ResolvedPhpDocBlock $resolvedPhpDocBlock): array
    {
        /** @var PhpDocNode $phpDocNode */
        $phpDocNode = $this->privatesAccessor->getPrivateProperty($resolvedPhpDocBlock, 'phpDocNode');

        return $phpDocNode->getTagsByName('@see');
    }
}
