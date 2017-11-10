<?php declare(strict_types=1);

namespace Symplify\CodingStandard\SniffTokenWrapper;

use PHP_CodeSniffer\Files\File;

final class PropertyWrapper
{
    public function createFromFileAndPosition(File $file)
    {
    }

    private function getDocCommentCloseTokenPosition()
    {
        return \SlevomatCodingStandard;
    }
}
