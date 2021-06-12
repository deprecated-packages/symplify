<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\ValueObjectFactory;

use Symfony\Component\Process\ExecutableFinder;
use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\GitWrapper;

final class GitWrapperFactory
{
    public function __construct(
        private ExecutableFinder $executableFinder
    ) {
    }

    public function create(): GitWrapper
    {
        $gitExecutable = $this->executableFinder->find('git');
        if ($gitExecutable === null) {
            throw new GitException('Unable to find the Git executable.');
        }

        return new GitWrapper($gitExecutable);
    }
}
