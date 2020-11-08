<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\ValueObject;

final class Option
{
    /**
     * @api
     * Do not change the value, it is internal PHPStan naming
     * @var string
     */
    public const LEVEL = 'level';

    /**
     * @api
     * Do not change the value, it is internal PHPStan naming
     * @var string
     */
    public const PATHS = 'paths';

    /**
     * @api
     * Do not change the value, it is internal PHPStan naming
     * @var string
     */
    public const CHECK_GENERIC_CLASS_IN_NON_GENERIC_OBJECT_TYPE = 'checkGenericClassInNonGenericObjectType';

    /**
     * @api
     * Do not change the value, it is internal PHPStan naming
     * @var string
     */
    public const REPORT_UNMATCHED_IGNORED_ERRORS = 'reportUnmatchedIgnoredErrors';

    /**
     * @api
     * Do not change the value, it is internal PHPStan naming
     * @var string
     */
    public const PARALLEL_MAX_PROCESSES = 'maximumNumberOfProcesses';

    /**
     * @api
     * Do not change the value, it is internal PHPStan naming
     * @var string
     */
    public const EXCLUDE_PATHS = 'excludes_analyse';

    /**
     * @var string
     */
    public const PATH = 'path';

    /**
     * @var string
     */
    public const OUTPUT_FILE = 'output-file';
}
