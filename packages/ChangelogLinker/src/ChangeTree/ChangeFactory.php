<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Symfony\Component\Process\Process;
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

    /**
     * @var DateTime[]
     */
    private $tagsWithDateTimes = [];

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->tagsWithDateTimes = $this->getTagsWithDateTimes();
    }

    /**
     * @param mixed[] $pullRequest
     */
    public function createFromPullRequest(array $pullRequest): Change
    {
        $message = sprintf('- [#%s] %s', $pullRequest['number'], $pullRequest['title']);

        $author = $pullRequest['user']['login'] ?? '';

        // skip the main maintainer to prevent self-thanking floods
        if ($author && ! in_array($author, $this->configuration->getAuthorsToIgnore(), true)) {
            $message .= ', Thanks to @' . $author;
        }

        $category = $this->resolveCategory($pullRequest['title']);
        $package = $this->resolvePackage($pullRequest['title']);
        $messageWithoutPackage = $this->resolveMessageWithoutPackage($message);
        $pullRequestTag = $this->resolveTag($pullRequest);

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

    /**
     * @inspiration https://stackoverflow.com/a/42191640/1348344
     * @return DateTime[]
     */
    private function getTagsWithDateTimes(): array
    {
        $tagsWithDatesProcess = new Process('git for-each-ref --format="%(refname:short) | %(creatordate)" refs/tags/');
        $tagsWithDatesProcess->run();

        $tagsWithDatesString = $tagsWithDatesProcess->getOutput();

        $tagsWithDatesLines = explode(PHP_EOL, $tagsWithDatesString);

        // remove empty values
        $tagsWithDatesLines = array_filter($tagsWithDatesLines);

        $tagsWithDateTimes = [];
        foreach ($tagsWithDatesLines as $tagsWithDatesLine) {
            [$tag, $date] = explode('|', $tagsWithDatesLine);
            $tagsWithDateTimes[trim($tag)] = DateTime::from($date);
        }

        return $tagsWithDateTimes;
    }

    /**
     * @param mixed[] $pullRequest
     */
    private function resolveTag(array $pullRequest): string
    {
        $pullRequestTag = 'Unreleased';

        if (isset($pullRequest['merged_at'])) {
            $mergeDateTime = DateTime::from($pullRequest['merged_at']);
            foreach ($this->tagsWithDateTimes as $tag => $tagDatTime) {
                // belongs to tag
                if ($tagDatTime > $mergeDateTime) {
                    $pullRequestTag = $tag;
                    break;
                }
            }
        }
        return $pullRequestTag;
    }
}
