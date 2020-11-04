<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree\Resolver;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\ValueObject\Option;
use Symplify\ChangelogLinker\ValueObject\PackageName;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

/**
 * @see \Symplify\ChangelogLinker\Tests\ChangeTree\ChangeFactory\Resolver\PackageResolverTest
 */
final class PackageResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/3oKiXw/1
     *
     * It assumes that there is at least one space after the package name.
     *
     * It covers:
     * - "[package-name] "Message => package-name
     * - "[aliased-package-name] "Message => aliased-package-name
     * - "[Aliased\PackageName] "Message => Aliased\PackageName
     */
    public const PACKAGE_NAME_REGEX = '#\[(?<package>[-\w\\\\]+)\]( ){1,}#';

    /**
     * @var string[]
     */
    private $packageAliases = [];

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->packageAliases = $parameterProvider->provideArrayParameter(Option::PACKAGE_ALIASES);
    }

    /**
     * E.g. "[ChangelogLinker] Add feature XY" => "ChangelogLinker"
     */
    public function resolvePackage(string $message): string
    {
        $match = Strings::match($message, self::PACKAGE_NAME_REGEX);
        if (! isset($match['package'])) {
            return PackageName::UNKNOWN;
        }

        return $this->packageAliases[$match['package']] ?? $match['package'];
    }
}
