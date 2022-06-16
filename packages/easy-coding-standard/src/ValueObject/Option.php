<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const FIX = 'fix';

    /**
     * @var string
     */
    public const CLEAR_CACHE = 'clear-cache';

    /**
     * @var string
     */
    public const NO_PROGRESS_BAR = 'no-progress-bar';

    /**
     * @var string
     */
    public const NO_ERROR_TABLE = 'no-error-table';

    /**
     * @var string
     */
    public const OUTPUT_FORMAT = 'output-format';

    /**
     * @api
     * @deprecated Use @see \Symplify\EasyCodingStandard\Config\ECSConfig::skip()
     * @var string
     */
    public const SKIP = 'skip';

    /**
     * Alias for SOURCE
     *
     * @api
     * @see \Symplify\EasyCodingStandard\Config\ECSConfig::paths()
     * @var string
     */
    public const PATHS = self::SOURCE;

    /**
     * @var string
     */
    public const SOURCE = 'source';

    /**
     * @api
     * @deprecated Use @see \Symplify\EasyCodingStandard\Config\ECSConfig::only()
     * @var string
     */
    public const ONLY = 'only';

    /**
     * @see \Symplify\EasyCodingStandard\Config\ECSConfig::cacheDirectory()
     * @var string
     */
    public const CACHE_DIRECTORY = 'cache_directory';

    /**
     * @deprecated Use @see \Symplify\EasyCodingStandard\Config\ECSConfig::lineEnding()
     * @var string
     */
    public const LINE_ENDING = 'line_ending';

    /**
     * @deprecated Use @see \Symplify\EasyCodingStandard\Config\ECSConfig::indentation()
     * @var string
     */
    public const INDENTATION = 'indentation';

    /**
     * @deprecated Use @see \Symplify\EasyCodingStandard\Config\ECSConfig::cacheNamespace()
     * @var string
     */
    public const CACHE_NAMESPACE = 'cache_namespace';

    /**
     * @see \Symplify\EasyCodingStandard\Config\ECSConfig::fileExtensions()
     * @var string
     */
    public const FILE_EXTENSIONS = 'file_extensions';

    /**
     * @var string
     */
    public const INDENTATION_SPACES = 'spaces';

    /**
     * @api
     * @var string
     */
    public const INDENTATION_TAB = 'tab';

    /**
     * @var string
     */
    public const XDEBUG = 'xdebug';

    /**
     * @var string
     */
    public const DEBUG = 'debug';

    /**
     * @see \Symplify\EasyCodingStandard\Config\ECSConfig::parallel()
     * @var string
     */
    public const PARALLEL = 'parallel';

    /**
     * @var string
     */
    public const CONFIG = 'config';

    /**
     * @see \Symplify\EasyCodingStandard\Config\ECSConfig::parallelJobSize()
     * @var string
     */
    public const PARALLEL_JOB_SIZE = 'parallel_job_size';

    /**
     * @see \Symplify\EasyCodingStandard\Config\ECSConfig::parallelPort()
     * @var string
     */
    public const PARALLEL_PORT = 'port';

    /**
     * @see \Symplify\EasyCodingStandard\Config\ECSConfig::parallelIdentifier()
     * @var string
     */
    public const PARALLEL_IDENTIFIER = 'identifier';

    /**
     * @see \Symplify\EasyCodingStandard\Config\ECSConfig::parallelMaxNumberOfProcesses()
     * @var string
     */
    public const PARALLEL_MAX_NUMBER_OF_PROCESSES = 'max-number-of-processes';

    /**
     * @var string
     */
    public const MEMORY_LIMIT = 'memory-limit';

    /**
     * @see \Symplify\EasyCodingStandard\Config\ECSConfig::parallelTimeoutInSeconds()
     * @var string
     */
    public const PARALLEL_TIMEOUT_IN_SECONDS = 'parallel-timeout-in-seconds';
}
