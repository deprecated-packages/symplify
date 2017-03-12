<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Translation\Latte;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Translation\Filesystem\ResourceFinder;
use Symplify\Statie\Translation\Latte\TranslationFilterProvider;
use Symplify\Statie\Translation\MessageAnalyzer;
use Symplify\Statie\Translation\TranslatorFactory;

final class TranslationFilterProviderTest extends TestCase
{
    /**
     * @var TranslationFilterProvider
     */
    private $translationFilterProvider;

    protected function setUp()
    {
        $this->translationFilterProvider = new TranslationFilterProvider(
            $this->createTranslator(),
            new MessageAnalyzer
        );
    }

    public function test()
    {
        $filters = $this->translationFilterProvider->getFilters();

        $this->assertSame(
            'field',
            $filters[TranslationFilterProvider::FILTER_NAME]('layout.field', 'cs')
        );

        $this->assertSame(
            'field',
            $filters[TranslationFilterProvider::FILTER_NAME]('layout.field', 'en')
        );
    }

    private function createTranslator(): TranslatorInterface
    {
        $translatorFactory = new TranslatorFactory(
            new Configuration(new NeonParser),
            new ResourceFinder
        );

        return $translatorFactory->create();
    }
}
