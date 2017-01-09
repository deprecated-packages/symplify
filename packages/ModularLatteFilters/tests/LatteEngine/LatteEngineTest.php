<?php

declare(strict_types = 1);

namespace Zenify\ModularLatteFilters\Tests\LatteEngine;

use Latte\Engine;
use Zenify\ModularLatteFilters\Tests\PHPUnit\AbstractContainerAwareTestCase;


final class LatteEngineTest extends AbstractContainerAwareTestCase
{

	public function test()
	{
		/** @var Engine $latte */
		$latte = $this->getServiceByType(Engine::class);
		$this->assertSame(10, $latte->invokeFilter('double', [5]));
	}

}
