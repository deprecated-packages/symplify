<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Translation;

use Symfony\Component\Translation\Translator;
use Symplify\Autodiscovery\Tests\AbstractAppKernelAwareTestCase;
use Symplify\Autodiscovery\Translation\TranslationPathAutodiscoverer;

/**
 * @see TranslationPathAutodiscoverer
 */
final class TranslationPathAutodiscoverTest extends AbstractAppKernelAwareTestCase
{
    public function test(): void
    {
        /** @var Translator $translator */
        $translator = $this->container->get('translator');

        $this->assertSame('two', $translator->trans('one'));
    }
}
