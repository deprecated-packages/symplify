<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\Twig;

use Symplify\Statie\Twig\TimeTwigExtension;

/**
 * @see TimeTwigExtension
 */
final class TimeTwigExtensionTest extends AbstractTwigExtensionTestCase
{
    public function test(): void
    {
        $value = $this->renderTemplate('{{ "12:25"|time_to_seconds }}');
        $this->assertSame(745, (int) $value);

        $value = $this->renderTemplate('{{ "1:12:25"|time_to_seconds }}');
        $this->assertSame(4345, (int) $value);
    }
}
