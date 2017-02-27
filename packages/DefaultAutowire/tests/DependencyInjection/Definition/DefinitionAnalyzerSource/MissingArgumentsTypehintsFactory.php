<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource;

use Symplify\DefaultAutowire\Tests\Source\SomeService;

final class MissingArgumentsTypehintsFactory
{
    /**
     * @param mixed $valueWithoutType
     * @param SomeService|null $someService
     * @param int $value
     */
    public function create(
        $valueWithoutType,
        ?SomeService $someService = null,
        int $value = 1
    ): MissingArgumentsTypehints {
        return new MissingArgumentsTypehints($valueWithoutType, $someService, $value);
    }
}
