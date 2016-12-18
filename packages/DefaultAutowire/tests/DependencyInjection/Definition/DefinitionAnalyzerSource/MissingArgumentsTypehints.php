<?php

declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource;

use Symplify\DefaultAutowire\Tests\Source\SomeService;

final class MissingArgumentsTypehints
{
    /**
     * @param string $valueWithoutType
     * @param SomeService|null $someService
     * @param int $value
     */
    public function __construct($valueWithoutType, SomeService $someService = null, $value = 1)
    {
    }
}
