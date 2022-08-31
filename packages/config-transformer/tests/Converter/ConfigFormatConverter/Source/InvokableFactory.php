<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Tests\Converter\ConfigFormatConverter\Source;

/**
 * @see https://symfony.com/doc/current/service_container/factories.html#invokable-factories
 */
final class InvokableFactory
{
    public function __invoke(): ExistingClass
    {
        return new ExistingClass();
    }
}
