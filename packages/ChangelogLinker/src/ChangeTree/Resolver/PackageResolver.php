<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree\Resolver;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Configuration\Package;

final class PackageResolver
{
    /**
     * @var string
     *
     * It assumes that tere is space after the package name.
     *
     * It covers:
     * - "[package-name] "Message => package-name
     * - "[aliased-package-name] "Message => aliased-package-name
     * - "[Aliased\PackageName] "Message => Aliased\PackageName
     */
    public const PACKAGE_NAME_PATTERN = '#\[(?<package>[-\w\\\\]+)\]( )#';

    /**
     * @var string[]
     */
    private $packageAliases = [];

    /**
     * @param string[] $packageAliases
     */
    public function __construct(array $packageAliases)
    {
        $this->packageAliases = $packageAliases;
    }

    /**
     * E.g. "[ChangelogLinker] Add feature XY" => "ChangelogLinker"
     */
    public function resolvePackage(string $message): ?string
    {
        $match = Strings::match($message, self::PACKAGE_NAME_PATTERN);
        if (! isset($match['package'])) {
            return Package::UNKNOWN;
        }

        return $this->packageAliases[$match['package']] ?? $match['package'];
    }
}
