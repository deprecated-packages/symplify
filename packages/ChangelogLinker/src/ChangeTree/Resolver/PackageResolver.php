<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree\Resolver;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Configuration\Package;

final class PackageResolver
{
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
        $match = Strings::match($message, '#\[(?<package>[-\w]+)\]#');
        if (! isset($match['package'])) {
            return Package::UNKNOWN;
        }

        return $this->packageAliases[$match['package']] ?? $match['package'];
    }
}
