<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Contract;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;

interface ArrayFixerInterface
{
    public function fixArrayOpener(Tokens $tokens, BlockInfo $blockInfo, int $index);
}
