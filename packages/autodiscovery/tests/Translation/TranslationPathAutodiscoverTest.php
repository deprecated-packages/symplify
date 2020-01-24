<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Translation;

use Symfony\Component\Translation\Translator;
use Symplify\Autodiscovery\Tests\Source\HttpKernel\AudiscoveryTestingKernel;
use Symplify\Autodiscovery\Translation\TranslationPathAutodiscoverer;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

/**
 * @see TranslationPathAutodiscoverer
 */
final class TranslationPathAutodiscoverTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernel(AudiscoveryTestingKernel::class);
    }

    public function test(): void
    {
        /** @var Translator $translator */
        $translator = static::$container->get('translator');

        $this->assertSame('two', $translator->trans('one'));
    }
}
