<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\ValueObjectFactory;

use Symplify\GitWrapper\GitWorkingCopy;
use Symplify\GitWrapper\GitWrapper;

/**
 * @api
 */
final class GitWorkingCopyFactory
{
    public function __construct(
        private GitWrapper $gitWrapper
    ) {
    }

    public function createWorkingCopy(string $directory): GitWorkingCopy
    {
        return new GitWorkingCopy($this->gitWrapper, $directory);
    }
}
