<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Twig\TwigTemplateAnalyzer\MissingClassConstantTwigAnalyzer\Source;

final class ExistingClass
{
    /**
     * @var string
     */
    public const EXISTING_CONSTANT = 'yes';
}
