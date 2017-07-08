<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ConstructorInjection;

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use SplFileInfo;

final class InjectToConstructorInjectionFixer implements DefinedFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Constructor injection should be used instead of @inject annotations and inject*() methods.',
            [
                // @todo: what is this for?
                new CodeSample(
                    '<?php
/**
 * @inject
 * @var stdClass
 */
public $property;'

                ),
                new CodeSample(
                    '<?php
/**
 * @var stdClass
 */
private $property;

public function injectValue(stdClass $stdClass)
{
    $this->stdClass = $stdClass;
}'

                ),

            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_CLASS) &&
            $tokens->isAnyTokenKindsFound([T_DOC_COMMENT, T_METHOD_C]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        // 1. find annotation @inject
        // array_reverse($elements, true)
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());
            $injectAnnotations = $doc->getAnnotationsOfType('inject');
            if (! count($injectAnnotations)) {
                continue;
            }

            // 1. remove it
            foreach ($injectAnnotations as $injectAnnotation) {
                $injectAnnotation->remove();
            }

            // 2. make public property private
            for ($i = $index; ; ++$i) {
                $token = $tokens[$i];
                if ($token->isGivenKind(T_PUBLIC)) {
                    $token->override([T_PRIVATE, 'private']);
                    break;
                }
            }
        }

        // 2. find method starting with inject*()
//        dump($tokens);
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }
}
