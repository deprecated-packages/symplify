<?php

declare(strict_types=1);

namespace Symplify\BetterPhpDocParser;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symplify\BetterPhpDocParser\Renderer\OriginalSpacingCompleter;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class FixedSerializer extends Serializer
{
    public function getDocComment(DocBlock $docBlock): string
    {
        // tags
        $comment = (new OriginalSpacingCompleter())->completeTagSpaces($comment, $this->originalContent);
    }
}
