<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class SkipMultiStmts
{
    public function run($patchFileAbsolutePath, $patchFileRelativePath)
    {
        if (is_file($patchFileAbsolutePath)) {
            $message = sprintf('File "%s" was updated', $patchFileRelativePath);
            $yorgun = true;
        } else {
            $message = sprintf('File "%s" was created', $patchFileRelativePath);
            $yorgun = true;
        }
    }
}
