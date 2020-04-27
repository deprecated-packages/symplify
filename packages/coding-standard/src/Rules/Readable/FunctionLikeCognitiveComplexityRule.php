<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules\Readable;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see https://www.tomasvotruba.com/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/
 */
final class FunctionLikeCognitiveComplexityRule implements Rule
{
    public function getNodeType(): string
    {
        return FunctionLike::class;
    }

    /**
     * @param FunctionLike $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        dump('@todo');
        die;

        return ['Do not use chained method calls'];
    }
}
