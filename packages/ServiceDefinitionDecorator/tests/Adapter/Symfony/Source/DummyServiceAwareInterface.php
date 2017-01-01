<?php

declare(strict_types=1);

namespace Symplify\ServiceDefinitionDecorator\Tests\Adapter\Symfony\Source;

interface DummyServiceAwareInterface
{
    public function setDummyService(DummyService $dummyService);
}
