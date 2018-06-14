<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Git;

use DateTimeInterface;
use Nette\Utils\DateTime;
use Symfony\Component\Process\Process;

final class DateToTagResolver
{
    /**
     * @var DateTimeInterface[]
     */
    private $tagsWithDateTimes = [];

    public function __construct()
    {
        $this->tagsWithDateTimes = $this->getTagsWithDateTimes();
    }

    public function resolveDateToTag(string $dateTime): string
    {
        $mainDateTime = DateTime::from($dateTime);

        $previousTag = 'Unreleased';

        // sort tags by version, e.g. v3.2 <= v3.22 <= v3.3
        uksort($this->tagsWithDateTimes, 'version_compare');

        // sort by date, to cover tags on secondary branches
        uasort(
            $this->tagsWithDateTimes,
            function (DateTimeInterface $firstDateTime, DateTimeInterface $secondDateTime) {
                return $firstDateTime > $secondDateTime;
            }
        );

        foreach ($this->tagsWithDateTimes as $tag => $tagDateTime) {
            // belongs to tag
            if ($mainDateTime < $tagDateTime) {
                return $tag;
            }
        }

        return 'Unreleased';
    }

    /**
     * @inspiration https://stackoverflow.com/a/42191640/1348344
     * @return DateTimeInterface[]
     */
    private function getTagsWithDateTimes(): array
    {
        $tagsWithDatesString = $this->getTagsWithDatesAsString();
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

    private function getTagsWithDatesAsString(): string
    {
        $tagsWithDatesProcess = new Process('git for-each-ref --format="%(refname:short) | %(creatordate)" refs/tags/');
        $tagsWithDatesProcess->run();

        return $tagsWithDatesProcess->getOutput();
    }
}
