<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Configuration\Configuration;

final class ChangeFactory
{
    /**
     * @var string
     */
    private const ADDED_PATTERN = '#(add|added|adds) #i';

    /**
     * @var string
     */
    private const FIXED_PATTERN = '#(fix(es|ed)?)#i';

    /**
     * @var string
     */
    private const CHANGED_PATTERN = '#( change| improve|( now )|bump|improve|allow)#i';

    /**
     * @var string
     */
    private const REMOVED_PATTERN = '#remove(d)?|delete(d)?#i';

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

        $messageWithoutPackage = $this->resolveMessageWithoutPackage($message);

        return new Change($message, $category, $package, $messageWithoutPackage);
    }

    private function resolveCategoryFromMessage(string $message): string
    {
        $match = Strings::match($message, self::ADDED_PATTERN);
        if ($match) {
            return 'Added';
        }

        $match = Strings::match($message, self::FIXED_PATTERN);
        if ($match) {
            return 'Fixed';
        }

        $match = Strings::match($message, self::CHANGED_PATTERN);
        if ($match) {
            return 'Changed';
        }

        $match = Strings::match($message, self::REMOVED_PATTERN);
        if ($match) {
            return 'Removed';
        }

        return Change::UNKNOWN_CATEGORY;
    }

    /**
     * E.g. "[ChangelogLinker] Add feature XY" => "ChangelogLinker"
     */
    private function resolvePackageFromMessage(string $message): ?string
    {
        $match = Strings::match($message, '#\[(?<package>[A-Za-z]+)\]#');

        if (! isset($match['package'])) {
            return Change::UNKNOWN_PACKAGE;
        }

        return $this->configuration->getPackageAliases()[$match['package']] ?? $match['package'];
    }

    private function resolveMessageWithoutPackage(string $message): string
    {
        $match = Strings::match($message, '#\[(?<package>[A-Za-z]+)\]#');

        if (! isset($match['package'])) {
            return $message;
        }

        return Strings::replace($message, '#\[' . $match['package'] . '\]\s+#');
    }
}
