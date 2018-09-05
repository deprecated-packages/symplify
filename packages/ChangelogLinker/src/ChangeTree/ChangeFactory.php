<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;

final class ChangeFactory
{
    /**
     * @var string
     */
    private const ADDED_PATTERN = '#\b(add(s|ed|ing)?)\b#i';

    /**
     * @var string
     */
    private const FIXED_PATTERN = '#\b(fix(es|ed|ing)?)\b#i';

    /**
     * @var string
     */
    private const CHANGED_PATTERN = '#\b(chang(e|es|ed|ing)|improv(e|es|ed|ing)|bump(s|ed|ing)?|(dis)?allow(s|ed|ing)?|return(s|ed|ing)?|renam(e|es|ed|ing)|decoupl(e|es|ed|ing)|now)\b#i';

    /**
     * @var string
     */
    private const REMOVED_PATTERN = '#\b(remov(e|es|ed|ing)|delet(e|es|ed|ing|)|drop(s|ped|ping)?)\b#i';

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

        return new Change($message, $category, $package, $messageWithoutPackage, $pullRequestTag);
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
