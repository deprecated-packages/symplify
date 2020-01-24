<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\DependencyInjection\Dummy;

use Symfony\Component\DependencyInjection\Compiler\ResolveBindingsPass;

/**
 * This class is needed due to Symfony requirements to autowire every parameters or throw an exception,
 * this is the only way so far to overcome such exception without bending architecture to code smells.
 *
 * Hopefully there will be better way in the future, like not enforcing exception.
 *
 * @see ResolveBindingsPass
 */
final class ResolveAutowiringExceptionHelper
{
    /**
     * @param string[] $namesToUrls
     * @param string[] $authorsToIgnore
     * @param string[] $packageAliases
     */
    public function __construct(
        string $repositoryName,
        string $repositoryUrl,
        array $namesToUrls,
        array $authorsToIgnore,
        array $packageAliases
    ) {
    }
}
