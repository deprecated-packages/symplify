<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Format YAML()
 * @method static Format YML()
 * @method static Format XML()
 * @method static Format PHP()
 *
 * @extends Enum<Format>
 */
final class Format extends Enum
{
    /**
     * @var string
     */
    private const YAML = 'yaml';

    /**
     * @var string
     */
    private const YML = 'yml';

    /**
     * @var string
     */
    private const XML = 'xml';

    /**
     * @var string
     */
    private const PHP = 'php';
}
