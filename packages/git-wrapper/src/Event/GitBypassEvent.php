<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Event;

/**
 * Event thrown if the command is flagged to skip execution.
 */
final class GitBypassEvent extends AbstractGitEvent
{
}
