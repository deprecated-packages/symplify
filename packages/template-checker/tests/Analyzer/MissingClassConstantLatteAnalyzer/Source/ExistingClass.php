<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Tests\Analyzer\MissingClassConstantLatteAnalyzer\Source;

final class ExistingClass
{
    /**
     * @var string
     */
    public const EXISTING_CONSTANT = 'yes';
}
