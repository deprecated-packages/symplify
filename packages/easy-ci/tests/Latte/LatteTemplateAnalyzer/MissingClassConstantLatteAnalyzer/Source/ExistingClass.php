<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Latte\LatteTemplateAnalyzer\MissingClassConstantLatteAnalyzer\Source;

final class ExistingClass
{
    /**
     * @var string
     */
    public const EXISTING_CONSTANT = 'yes';

    /**
     * @var int
     */
    public const EXISTING_CONSTANT_WITH_100_NUMBER = 100;
}
