<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const SOURCE = 'source';

    /**
     * @var string
     */
    public const DRY_RUN = 'dry-run';

    /**
     * @var string
     */
    public const ERROR_REPORT_FILE = 'error-report-file';
}
