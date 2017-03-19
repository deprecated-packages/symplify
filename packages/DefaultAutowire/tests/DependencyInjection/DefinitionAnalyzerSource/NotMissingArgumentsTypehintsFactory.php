<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\DefinitionAnalyzerSource;

use Symplify\DefaultAutowire\Tests\Source\SomeService;

final class NotMissingArgumentsTypehintsFactory
{
    public function create(SomeService $someService, SomeService $anotherService): NotMissingArgumentsTypehints
    {
        return new NotMissingArgumentsTypehints($someService, $anotherService);
    }
}
