<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ConstructorInjection;

use Nette\PhpGenerator\Method;
use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use SplFileInfo;
use Twig\Token;

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
            if (! $token->isGivenKind(T_DOC_COMMENT)) {
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

            // 3. add dependency to constructor
            $propertyName = '';
            for ($i = $index; ; ++$i) {
                $token = $tokens[$i];
                if ($token->isGivenKind(T_VARIABLE)) {
                    $propertyName = ltrim($token->getContent(), '$');
                    break;
                }
            }
            $varAnnotation = $doc->getAnnotationsOfType('var')[0];
            $propertyType = $varAnnotation->getTypes()[0];

            $this->addConstructorMethod($tokens, $propertyType, $propertyName);
        }

        // 2. find method starting with inject*()
//        foreach ($tokens as $index => $token) {
//            if (! $token->isGivenKind(T_FUNCTION)) {
//                continue;
//            }
//
//
//            dump($token);
//            die;
//        }
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

    private function addConstructorMethod(Tokens $tokens, string $propertyType, string $propertyName): void
    {
        $method = new Method('__construct');
        $method->setVisibility('public');

        $parameter = $method->addParameter($propertyName);
        $parameter->setTypeHint($propertyType);
        $method->setBody('$this->? = $?;', [$propertyName, $propertyName]);

        // indent method code with 4 spaces
        $methodCode = Strings::indent((string) $method, 1, '    ');

        $constructorPosition = $this->getConstructorPosition($tokens);

        $constructorTokens = Tokens::fromCode(sprintf('<?php class SomeClass { %s }', $methodCode));

        $constructorTokens->clearRange(0, 5); // drop initial code, like php tag opening
        $constructorTokens->clearRange(30, 31); // drop initial code, like php tag opening
        $constructorTokens->clearEmptyTokens();

        $tokens->insertAt($constructorPosition, $constructorTokens);
    }

    private function getConstructorPosition(Tokens $tokens): int
    {
        // 1. after last property
        for ($index = count($tokens) - 1; $index > 1; --$index) {
            $token = $tokens[$index];
            if ($token->isGivenKind(T_VARIABLE)) {
                $propertyEndSemicolonPosition = $tokens->getNextTokenOfKind($index, [';']);
                return $propertyEndSemicolonPosition + 1;
            }
        }

        // 2. before first method
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_FUNCTION)) {
                $methodStartPosition = $tokens->getPrevTokenOfKind($index, [T_PUBLIC, T_PRIVATE, T_PROTECTED, T_DOC_COMMENT]);
                // @todo test
                return $methodStartPosition - 1;
            }
        }
    }
}
