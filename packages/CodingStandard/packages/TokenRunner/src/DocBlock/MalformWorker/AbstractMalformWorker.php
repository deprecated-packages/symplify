<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;

abstract class AbstractMalformWorker implements MalformWorkerInterface
{
    /**
     * @var FunctionsAnalyzer
     */
    private $functionsAnalyzer;

    public function __construct(FunctionsAnalyzer $functionsAnalyzer)
    {
        $this->functionsAnalyzer = $functionsAnalyzer;
    }

    /**
     * @return string[]
     */
    protected function getDocRelatedArgumentNames(Tokens $tokens, int $docTokenPosition): ?array
    {
        $functionTokenPosition = $tokens->getNextTokenOfKind($docTokenPosition, [new Token([T_FUNCTION, 'function'])]);
        if ($functionTokenPosition === null) {
            return null;
        }

        $functionArgumentAnalyses = $this->functionsAnalyzer->getFunctionArguments($tokens, $functionTokenPosition);

        return array_keys($functionArgumentAnalyses);
    }
}
