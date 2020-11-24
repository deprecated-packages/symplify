<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const INPUT_FORMAT = 'input-format';

    /**
     * @var string
     */
    public const OUTPUT_FORMAT = 'output-format';

    /**
     * @var string
     */
    public const BC_LAYER = 'bc-layer';

    /**
     * @var string
     */
    public const TARGET_SYMFONY_VERSION = 'target-symfony-version';

    /**
     * @var string
     */
    public const DRY_RUN = 'dry-run';
}
