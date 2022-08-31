<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Tests\Converter\ConfigFormatConverter\Source;

/**
 * @see https://symfony.com/doc/current/service_container/factories.html#static-factories
 */
final class StaticFactory
{
    public function create(): ExistingClass
    {
        return new ExistingClass();
    }
}
