<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
use Symplify\TokenRunner\DocBlock\MalformWorker\MissingParamNameMalformWorker;
use Symplify\TokenRunner\DocBlock\MalformWorker\ParamNameTypoMalformWorker;
use Symplify\TokenRunner\DocBlock\MalformWorker\ParamTypeAndNameMalformWorker;
use Symplify\TokenRunner\DocBlock\MalformWorker\SuperfluousReturnNameMalformWorker;

/**
 * @see ParamNameTypoMalformWorker
 * @see MissingParamNameMalformWorker
 * @see ParamTypeAndNameMalformWorker
 * @see SuperfluousReturnNameMalformWorker
 */
final class ParamAndReturnTagMalformsFixer extends AbstractSymplifyFixer
{
    /**
     * @var MalformWorkerInterface[]
     */
    private $malformWorkers = [];

    /**
     * @param MalformWorkerInterface[] $malformWorkers
     */
    public function __construct(array $malformWorkers)
    {
        $this->malformWorkers = $malformWorkers;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'In @param type should be before the $name',
            [new CodeSample('<?php
/**
 * @param $name type  
 */
function someFunction(type $name)
{
}
')]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_FUNCTION, T_DOC_COMMENT]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->reverseTokens($tokens) as $index => $token) {
            if (! $token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $docContent = $token->getContent();
            if (! Strings::match($docContent, '#@(param|return)#')) {
                continue;
            }

            $originalDocContent = $docContent;
            foreach ($this->malformWorkers as $malformWorker) {
                $docContent = $malformWorker->work($docContent, $tokens, $index);
            }

            if ($docContent === $originalDocContent) {
                continue;
            }

            $tokens[$index] = new Token([T_DOC_COMMENT, $docContent]);
        }
    }

    public function getPriority(): int
    {
        return $this->getPriorityBefore(PhpdocAlignFixer::class);
    }
}
