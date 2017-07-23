<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\DependencyInjection;

use Nette\PhpGenerator\Method;
use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class InjectToConstructorInjectionFixer implements DefinedFixerInterface
{
    /**
     * @var string
     */
    private const VAR_NAME = 'var';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Constructor injection should be used instead of @inject annotations.',
            [
                new CodeSample(
'<?php
class SomeClass
{
    /**
     * @inject
     * @var stdClass
     */
    public $property;
}'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_DOC_COMMENT, T_PUBLIC, T_VARIABLE]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        // 1. find annotation @inject
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
            $visibilityTokenPosition = $tokens->getNextMeaningfulToken($index);
            $visibilityToken = $tokens[$visibilityTokenPosition];
            if ($visibilityToken->isGivenKind(T_PUBLIC)) {
                $visibilityToken->override([T_PRIVATE, 'private']);
            } else {
                // probably not a property with @inject annotation
                continue;
            }

            // save changed annotation
            $token->setContent($doc->getContent());


            // 3. add dependency to constructor
            $propertyNameTokenPosition = $tokens->getNextMeaningfulToken($visibilityTokenPosition);
            $propertyNameToken = $tokens[$propertyNameTokenPosition];
            $propertyName = ltrim($propertyNameToken->getContent(), '$');

            // detect constructor
            $constructMethodPosition = null;
            for ($i = $index; $i < count($tokens); ++$i) {
                $token = $tokens[$i];
                if ($token->isGivenKind(T_FUNCTION)) {
                    $namePosition = $tokens->getNextMeaningfulToken($i);
                    $methodNameToken = $tokens[$namePosition];
                    if (strtolower($methodNameToken->getContent()) === '__construct') {
                        $constructMethodPosition = $i;
                        break;
                    }
                }
            }

            $varAnnotation = $doc->getAnnotationsOfType(self::VAR_NAME)[0];
            $propertyType = $varAnnotation->getTypes()[0];

            // A. has a constructor?
            if (is_int($constructMethodPosition)) { // "function" token
                $this->addPropertyToConstructor($tokens, $propertyType, $propertyName, $constructMethodPosition);
            } else {
                // B. doesn't have a constructor
                $this->addConstructorMethod($tokens, $propertyType, $propertyName);
            }

            // run again with new tokens; @todo: can this be done any better to notice new __construct method added
            // by these tokens?
            $this->fix($file, $tokens);
            break;
        }
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
        $constructorPosition = $this->getConstructorPosition($tokens);
        $constructorTokens = $this->createConstructorWithPropertyCodeInTokens($propertyType, $propertyName);

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
                $methodStartPosition = $tokens->getPrevTokenOfKind(
                    $index,
                    [T_PUBLIC, T_PRIVATE, T_PROTECTED, T_DOC_COMMENT]
                );

                return $methodStartPosition - 1;
            }
        }
    }

    private function createConstructorWithPropertyCodeInTokens(string $propertyType, string $propertyName): Tokens
    {
        $indentedConstructorCode = $this->createConstructorWithPropertyCodeInString($propertyType, $propertyName);
        $constructorTokens = Tokens::fromCode(sprintf('<?php class SomeClass {

%s
}', $indentedConstructorCode));

        $constructorTokens->clearRange(0, 5); // drop initial code: "<?php class SomeClass {"
        $constructorTokens->clearRange(30, 31); // drop closing code: "}"

        $constructorTokens->clearEmptyTokens();

        return $constructorTokens;
    }

    private function createConstructorWithPropertyCodeInString(string $propertyType, string $propertyName): string
    {
        $method = new Method('__construct');
        $method->setVisibility('public');

        $parameter = $method->addParameter($propertyName);
        $parameter->setTypeHint($propertyType);
        $method->setBody('$this->? = $?;', [$propertyName, $propertyName]);

        $methodAsString = (string) $method;
        // tabs are used by default as indent; use spaces instead
        $methodAsString = str_replace("\t", '    ', $methodAsString);

        // indent method code with 4 spaces
        return Strings::indent($methodAsString, 1, '    ');
    }

    private function addPropertyToConstructor(
        Tokens $tokens,
        string $propertyType,
        string $propertyName,
        int $constructMethodPosition
    ): void {
        $startParenthesisIndex = $tokens->getNextTokenOfKind($constructMethodPosition, ['(', ';', [T_CLOSE_TAG]]);
        if (! $tokens[$startParenthesisIndex]->equals('(')) {
            return;
        }

        $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex);

        // add property as last argument: ", Type $property"
        $tokens->insertAt($endParenthesisIndex, [
            new Token(','),
            new Token([T_WHITESPACE, ' ']),
            new Token([T_STRING, $propertyType]),
            new Token([T_WHITESPACE, ' ']),
            new Token([T_VARIABLE, '$' . $propertyName]),
        ]);

        // detect end brace
        $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex);
        $startBraceIndex = $tokens->getNextTokenOfKind($endParenthesisIndex, [';', '{']);
        $endBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startBraceIndex);

        // add property as last assignment: "$this->property = $property;"
        $tokens->insertAt($endBraceIndex - 1, [
            new Token([T_WHITESPACE, PHP_EOL . '        ']), // 2x indent with spaces
            new Token([T_VARIABLE, '$this']),
            new Token([T_OBJECT_OPERATOR, '->']),
            new Token([T_STRING, $propertyName]),
            new Token([T_WHITESPACE, ' ']),
            new Token('='),
            new Token([T_WHITESPACE, ' ']),
            new Token([T_VARIABLE, '$' . $propertyName]),
            new Token(';'),
        ]);
    }
}
