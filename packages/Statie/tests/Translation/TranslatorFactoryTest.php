<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Translation;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Translation\Filesystem\ResourceFinder;
use Symplify\Statie\Translation\TranslatorFactory;

final class TranslatorFactoryTest extends TestCase
{
    /**
     * @var TranslatorFactory
     */
    private $translatorFactory;

    protected function setUp(): void
    {
        $configuration = new Configuration(new NeonParser);
        $configuration->setSourceDirectory(__DIR__ . '/TranslatorFactorySource');

        $this->translatorFactory = new TranslatorFactory($configuration, new ResourceFinder);
    }

    public function test(): void
    {
        $translator = $this->translatorFactory->create();

        $this->assertSame('', $translator->getLocale());

        $this->assertSame('Políček', $translator->trans('field', [], 'layout', 'cs'));
        $this->assertSame('Slap', $translator->trans('field', [], 'layout', 'en'));

        $this->assertSame('totallyLost', $translator->trans('totallyLost', [], 'missing', 'cs'));
    }
}
