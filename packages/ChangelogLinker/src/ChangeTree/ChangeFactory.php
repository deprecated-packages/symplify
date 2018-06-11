<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Configuration\Configuration;

final class ChangeFactory
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function createFromMessage(string $message): Change
    {
        $category = $this->resolveCategoryFromMessage($message);
        $package = $this->resolvePackageFromMessage($message);

        return new Change($message, $category, $package);
    }

    private function resolveCategoryFromMessage(string $message): string
    {
        $match = Strings::match($message, '#(Add)#');
        if ($match) {
            return 'Added';
        }

        $match = Strings::match($message, '#(Fix)#');
        if ($match) {
            return 'Fixed';
        }

        $match = Strings::match($message, '#( change| improve|( now )|Bump|improve|allow)#');
        if ($match) {
            return 'Changed';
        }

        return 'Unknown Category';
    }

    /**
     * E.g. "[ChangelogLinker] Add feature XY" => "ChangelogLinker"
     */
    private function resolvePackageFromMessage(string $change): ?string
    {
        $match = Strings::match($change, '#\[(?<package>[A-Za-z]+)\]#');

        if (! isset($match['package'])) {
            return 'Unknown Package';
        }

        return $this->configuration->getPackageAliases()[$match['package']] ?? $match['package'];
    }
}
