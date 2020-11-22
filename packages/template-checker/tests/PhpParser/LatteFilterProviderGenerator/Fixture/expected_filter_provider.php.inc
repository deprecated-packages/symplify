<?php

declare (strict_types=1);

namespace App\Latte\FilterProvider;

final class SomeMethodFilterProvider implements \App\Contract\Latte\FilterProviderInterface
{
    public const FILTER_NAME = 'someMethod';
    public function getName() : string
    {
        return self::FILTER_NAME;
    }
    public function __invoke(string $name) : int
    {
        return \Symplify\TemplateChecker\Tests\PhpParser\LatteFilterProviderGenerator\Source\SomeHelper::someMethod($name);
    }
}
