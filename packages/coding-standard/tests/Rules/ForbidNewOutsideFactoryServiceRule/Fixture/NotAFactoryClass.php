<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbidNewOutsideFactoryServiceRule\Fixture;

final class NotAFactoryClass
{
    public function create()
	{
		return new CarSearch();
	}
}
