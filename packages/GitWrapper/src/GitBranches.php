<?php declare(strict_types=1);

namespace Symplify\GitWrapper;

use ArrayIterator;
use IteratorAggregate;

/**
 * Class that parses and returnes an array of branches.
 */
final class GitBranches implements IteratorAggregate
{
    /**
     * @var GitWorkingCopy
     */
    private $gitWorkingCopy;

    public function __construct(GitWorkingCopy $gitWorkingCopy)
    {
        $this->gitWorkingCopy = clone $gitWorkingCopy;
        $output = (string) $gitWorkingCopy->branch(['a' => true]);
    }

    /**
     * Fetches the branches via the `git branch` command.
     *
     * @return string[]
     */
    public function fetchBranches(bool $onlyRemote = false): array
    {
        $this->gitWorkingCopy->clearOutput();
        $options = ($onlyRemote) ? ['r' => true] : ['a' => true];
        $output = (string) $this->gitWorkingCopy->branch($options);
        $branches = preg_split("/\r\n|\n|\r/", rtrim($output));
        return array_map([$this, 'trimBranch'], $branches);
    }

    public function trimBranch(string $branch): string
    {
        return ltrim($branch, ' *');
    }

    public function getIterator()
    {
        $branches = $this->all();
        return new ArrayIterator($branches);
    }

    /**
     * @return string[]
     */
    public function all(): array
    {
        return $this->fetchBranches();
    }

    /**
     * @return string[]
     */
    public function remote(): array
    {
        return $this->fetchBranches(true);
    }

    public function head(): string
    {
        return trim((string) $this->gitWorkingCopy->run(['rev-parse --abbrev-ref HEAD']));
    }
}
