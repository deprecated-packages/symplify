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

    private DateTimeImmutable $authorDate;

    /**
     * @var string
     */
    private $committer;

    private DateTimeImmutable $committerDate;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body;

    public function __construct(array $data)
    {
        $this->hash = $data['hash'];
        $this->author = $data['author'];
        $parsed = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $data['authorDate']);

        if (! $parsed) {
            throw new GitException('Could not parse author date.');
        }

        $this->authorDate = $parsed;
        $this->committer = $data['committer'];

        $parsed = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $data['committerDate']);

        if (! $parsed) {
            throw new GitException('Could not parse commiter date.');
        }

        $this->committerDate = $parsed;
        $this->subject = $data['subject'];
        $this->body = $data['body'];
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
