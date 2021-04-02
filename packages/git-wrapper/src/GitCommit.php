<?php

declare(strict_types=1);

namespace Symplify\GitWrapper;

use DateTime;
use DateTimeImmutable;
use Symplify\GitWrapper\Exception\GitException;

final class GitCommit
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $author;

    /**
     * @var DateTimeImmutable
     */
    private $authorDate;

    /**
     * @var string
     */
    private $committer;

    /**
     * @var DateTimeImmutable
     */
    private $committerDate;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body;

    public function __construct(
        string $hash,
        string $author,
        string $authorDate,
        string $committer,
        string $committerDate,
        string $subject,
        string $body
    ) {
        $this->hash = $hash;
        $this->author = $author;
        $parsed = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $authorDate);

        if (! $parsed) {
            throw new GitException('Could not parse author date.');
        }

        $this->authorDate = $parsed;
        $this->committer = $committer;

        $parsed = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $committerDate);

        if (! $parsed) {
            throw new GitException('Could not parse commiter date.');
        }

        $this->committerDate = $parsed;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getAuthorDate(): DateTimeImmutable
    {
        return $this->authorDate;
    }

    public function getCommitter(): string
    {
        return $this->committer;
    }

    public function getCommitterDate(): DateTimeImmutable
    {
        return $this->committerDate;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
