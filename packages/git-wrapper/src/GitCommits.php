<?php

declare(strict_types=1);

namespace Symplify\GitWrapper;

use ArrayIterator;
use IteratorAggregate;
use Nette\Utils\Strings;
use Symplify\GitWrapper\ValueObject\CommandName;
use Symplify\GitWrapper\ValueObject\Regex;

/**
 * Class that parses and returnes an array of commits.
 */
final class GitCommits implements IteratorAggregate
{
    /**
     * @var string[]
     */
    private const FORMAT_LINES = [
        'Hash: %H',
        'Author: %an <%ae>',
        'AuthorDate: %aI',
        'Committer: %cn <%ce>',
        'CommitterDate: %cI',
        'Subject: %s',
        'Body: %b',
    ];

    /**
     * @var GitWorkingCopy
     */
    private $gitWorkingCopy;

    public function __construct(GitWorkingCopy $gitWorkingCopy)
    {
        $this->gitWorkingCopy = clone $gitWorkingCopy;
    }

    /**
     * @return ArrayIterator<string>
     */
    public function getIterator(): ArrayIterator
    {
        $commits = $this->all();

        return new ArrayIterator($commits);
    }

    /**
     * @return string[]
     * @api
     */
    public function all(): array
    {
        return $this->fetchCommits();
    }

    /**
     * Fetches the commits via the `git log` command.
     *
     * @return string[]
     * @api
     */
    public function fetchCommits(): array
    {
        $output = $this->gitWorkingCopy->log([
            'format=%H' => '',
        ]);

        return Strings::split(trim($output), Regex::NEWLINE_REGEX);
    }

    /**
     * Fetches a range of commits via the `git rev-list` command.
     *
     * @return string[]
     * @api
     */
    public function fetchRange(string $first, string $second): array
    {
        $output = $this->gitWorkingCopy->run(
            CommandName::REV_LIST,
            [
                $first . '...' . $second,
                [
                    'boundary' => true,
                    'reverse' => true,
                ],
            ]
        );

        return array_map(function (string $commit): string {
            return $this->parseCommit($commit);
        }, Strings::split(trim($output), Regex::NEWLINE_REGEX));
    }

    public function get(string $hash): GitCommit
    {
        $format = implode('%n', self::FORMAT_LINES);

        $output = $this->gitWorkingCopy->show($hash, [
            'format=' . $format => '',
            'no-patch' => true,
        ]);

        return $this->parseShowOutput($output);
    }

    private function parseShowOutput(string $output): GitCommit
    {
        /** @var string[] $lines */
        $lines = Strings::split(trim($output), Regex::NEWLINE_REGEX);
        $items = [];

        $captureBody = false;

        foreach ($lines as $line) {
            if (! $captureBody) {
                $split = Strings::split($line, '#:#');
                $key = array_shift($split);
                $value = trim((implode(':', $split)));

                if ($key === 'Body') {
                    $captureBody = true;
                }

                $items[Strings::firstLower($key)] = $value;
            } else {
                $items['body'] .= "\n" . $line;
            }
        }

        return new GitCommit($items);
    }

    private function parseCommit(string $commit): string
    {
        return ltrim($commit, '-');
    }
}
