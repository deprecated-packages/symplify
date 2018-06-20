<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\DependencyInjection\Dummy;

/**
 * This class is needed due to Symfony requirements to autowire every parameters or throw an exception,
 * this is the only way so far to overcome such exception without bending architecture to code smells.
 *
 * Hopefully there will be better way in the future, like not enforcing exception.
 */
final class ResolveAutowiringExceptionHelper
{
    public function __construct(string $repositoryName, string $repositoryUrl)
    {
    }
}
