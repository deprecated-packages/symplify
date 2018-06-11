<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

use Nette\Utils\Strings;

final class ChangeFactory
{
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

        $match = Strings::match($message, '#(Change|( now )|Bump|improve|allow)#');
        if ($match) {
            return 'Changed';
        }

        return 'Unknown Category';
    }

    private function resolvePackageFromMessage(string $change): ?string
    {
        $match = Strings::match($change, '#\[(?<package>[A-Za-z]+)\]#');

        return $match['package'] ?? 'Unknown Package';
    }
}
