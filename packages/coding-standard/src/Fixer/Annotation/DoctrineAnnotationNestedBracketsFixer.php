<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Annotation;

use Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Doctrine\Annotation\Tokens as DoctrineAnnotationTokens;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenAnalyzer\DoctrineAnnotationElementAnalyzer;
use Symplify\CodingStandard\TokenAnalyzer\DoctrineAnnotationNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

final class DoctrineAnnotationNestedBracketsFixer extends AbstractSymplifyFixer implements ConfigurableRuleInterface, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ANNOTATION_CLASSES = 'annotation_classes';

    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Adds nested curly brackets to defined annotations, see https://github.com/doctrine/annotations/issues/418';

    /**
     * @var string[]
     */
    private array $annotationClasses = [];

    public function __construct(
        private DoctrineAnnotationElementAnalyzer $doctrineAnnotationElementAnalyzer,
        private DoctrineAnnotationNameResolver $annotationNameResolver,
        private NamespaceUsesAnalyzer $namespaceUsesAnalyzer
    ) {
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
/**
* @MainAnnotation(
*     @NestedAnnotation(),
*     @NestedAnnotation(),
* )
*/
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
/**
* @MainAnnotation({
*     @NestedAnnotation(),
*     @NestedAnnotation(),
* })
*/
CODE_SAMPLE
                ,
                [
                    self::ANNOTATION_CLASSES => ['MainAnnotation'],
                ]
            ),
        ]);
    }

    /**
     * @param array<string, string[]> $configuration
     */
    public function configure(array $configuration): void
    {
        $annotationsClasses = $configuration[self::ANNOTATION_CLASSES] ?? [];
        Assert::isArray($annotationsClasses);
        Assert::allString($annotationsClasses);

        $this->annotationClasses = $annotationsClasses;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function fix(\SplFileInfo $fileInfo, Tokens $tokens): void
    {
        $useDeclarations = $this->namespaceUsesAnalyzer->getDeclarationsFromTokens($tokens);

        // fetch indexes one time, this is safe as we never add or remove a token during fixing

        /** @var \PhpCsFixer\Tokenizer\Token[] $docCommentTokens */
        $docCommentTokens = $tokens->findGivenKind(T_DOC_COMMENT);
        foreach ($docCommentTokens as $index => $docCommentToken) {
            if (! $this->doctrineAnnotationElementAnalyzer->detect($tokens, $index)) {
                continue;
            }

            $doctrineAnnotationTokens = DoctrineAnnotationTokens::createFromDocComment($docCommentToken, []);
            $this->fixAnnotations($doctrineAnnotationTokens, $useDeclarations);

            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([T_DOC_COMMENT, $doctrineAnnotationTokens->getCode()]);
        }
    }

    /**
     * @param DoctrineAnnotationTokens<Token> $tokens
     */
    private function fixAnnotations(DoctrineAnnotationTokens $tokens, $useDeclarations): void
    {
        foreach ($tokens as $index => $token) {
            $isAtToken = $tokens[$index]->isType(DocLexer::T_AT);
            if (! $isAtToken) {
                continue;
            }

            $annotationName = $this->annotationNameResolver->resolveName($tokens, $index, $useDeclarations);
            if ($annotationName === null) {
                continue;
            }

            if (! in_array($annotationName, $this->annotationClasses, true)) {
                continue;
            }

            $closingBraceIndex = $tokens->getAnnotationEnd($index);
            if ($closingBraceIndex === null) {
                continue;
            }

            $braceIndex = $tokens->getNextMeaningfulToken($index + 1);
            if ($braceIndex === null) {
                continue;
            }

            /** @var Token $braceToken */
            $braceToken = $tokens[$braceIndex];
            if (! $this->doctrineAnnotationElementAnalyzer->isOpeningBracketFollowedByAnnotation(
                $braceToken,
                $tokens,
                $braceIndex
            )) {
                continue;
            }

            // add closing brace
            $tokens->insertAt($closingBraceIndex, new Token(DocLexer::T_OPEN_CURLY_BRACES, '}'));

            // add opening brace
            $tokens->insertAt($braceIndex + 1, new Token(DocLexer::T_OPEN_CURLY_BRACES, '{'));
        }
    }
}
