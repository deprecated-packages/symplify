<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\ValueObject;

final class CommandName
{
    /**
     * @var string
     */
    public const REBASE = 'rebase';

    /**
     * @var string
     */
    public const REMOTE = 'remote';

    /**
     * @var string
     */
    public const FETCH = 'fetch';

    /**
     * @var string
     */
    public const PUSH = 'push';

    /**
     * @var string
     */
    public const COMMIT = 'commit';

    /**
     * @var string
     */
    public const CONFIG = 'config';

    /**
     * @var string
     */
    public const DIFF = 'diff';

    /**
     * @var string
     */
    public const GREP = 'grep';

    /**
     * @var string
     */
    public const INIT = 'init';

    /**
     * @var string
     */
    public const LOG = 'log';

    /**
     * @var string
     */
    public const MERGE = 'merge';

    /**
     * @var string
     */
    public const PULL = 'pull';

    /**
     * @var string
     */
    public const RM = 'rm';

    /**
     * @var string
     */
    public const SHOW = 'show';

    /**
     * @var string
     */
    public const STATUS = 'status';

    /**
     * @var string
     */
    public const TAG = 'tag';

    /**
     * @var string
     */
    public const CLEAN = 'clean';

    /**
     * @var string
     */
    public const ARCHIVE = 'archive';

    /**
     * @var string
     */
    public const REV_PARSE = 'rev-parse';

    /**
     * @var string
     */
    public const MV = 'mv';

    /**
     * @var string
     */
    public const RESET = 'reset';

    /**
     * @var string
     */
    public const CHECKOUT = 'checkout';

    /**
     * @var string
     */
    public const APPLY = 'apply';

    /**
     * @var string
     */
    public const BISECT = 'bisect';

    /**
     * @var string
     */
    public const CLONE = 'clone';

    /**
     * @var string
     */
    public const BRANCH = 'branch';

    /**
     * @var string
     */
    public const ADD = 'add';

    /**
     * @var string
     */
    public const MERGE_BASE = 'merge-base';
}
