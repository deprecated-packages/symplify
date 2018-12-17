<?php declare(strict_types=1);

namespace Symplify\FlexLoader\Tests\Flex\FlexLoader;

use PHPUnit\Framework\TestCase;
use Symplify\FlexLoader\Exception\ConfigurationException;
use Symplify\FlexLoader\Tests\Flex\FlexLoader\Source\SwapKernel;

final class FlexLoaderArgumentSwapTest extends TestCase
{
    public function test(): void
    {
        $this->expectException(ConfigurationException::class);
        new SwapKernel('production', false);
    }
}
