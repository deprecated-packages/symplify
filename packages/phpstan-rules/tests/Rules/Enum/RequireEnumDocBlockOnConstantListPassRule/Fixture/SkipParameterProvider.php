<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Fixture;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Source\ParameterName;

final class SkipParameterProvider
{
    public function __construct(ParameterProvider $parameterProvider)
    {
        return $parameterProvider->provideParameter(ParameterName::SOURCE);
    }
}
