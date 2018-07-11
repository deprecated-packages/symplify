<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;

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
    private const CHANGED_PATTERN = '#( change| improve|( now )|bump|improve|allow|return|rename|decouple)#i';

    /**
     * @var string
     */
    private const REMOVED_PATTERN = '#remove(d)?|delete(d)|drop|dropped?#i';

    /**
     * @var GitCommitDateTagResolver
     */
    private $gitCommitDateTagResolver;

    /**
     * @var string[]
     */
    private $packageAliases = [];

    /**
     * @var string[]
     */
    private $authorsToIgnore = [];

    /**
     * @param string[] $packageAliases
     * @param string[] $authorsToIgnore
     */
    public function __construct(
        GitCommitDateTagResolver $gitCommitDateTagResolver,
        array $packageAliases,
        array $authorsToIgnore
    ) {
        $this->gitCommitDateTagResolver = $gitCommitDateTagResolver;
        $this->packageAliases = $packageAliases;
        $this->authorsToIgnore = $authorsToIgnore;
    }

    /**
     * @param mixed[] $pullRequest
     */
    public function createFromPullRequest(array $pullRequest): Change
    {
        $message = sprintf('- [#%s] %s', $pullRequest['number'], $pullRequest['title']);

        $author = $pullRequest['user']['login'] ?? '';

        // skip the main maintainer to prevent self-thanking floods
        if ($author && ! in_array($author, $this->authorsToIgnore, true)) {
            $message .= ', Thanks to @' . $author;
        }

        $category = $this->resolveCategory($pullRequest['title']);
        $package = $this->resolvePackage($pullRequest['title']);
        $messageWithoutPackage = $this->resolveMessageWithoutPackage($message);

        // @todo 'merge_commit_sha' || 'head'
        $pullRequestTag = $this->gitCommitDateTagResolver->resolveCommitToTag($pullRequest['merge_commit_sha']);

        return new Change($message, $category, $package, $messageWithoutPackage, $author, $pullRequestTag);
    }

    private function resolveCategory(string $message): string
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
    private function resolvePackage(string $message): ?string
    {
        $match = Strings::match($message, '#\[(?<package>\w+)\]#');

        if (! isset($match['package'])) {
            return Change::UNKNOWN_PACKAGE;
        }

        return $this->packageAliases[$match['package']] ?? $match['package'];
    }

    private function resolveMessageWithoutPackage(string $message): string
    {
        $match = Strings::match($message, '#\[(?<package>\w+)\]#');

        if (! isset($match['package'])) {
            return $message;
        }

        return Strings::replace($message, '#\[' . $match['package'] . '\]\s+#');
    }
}
