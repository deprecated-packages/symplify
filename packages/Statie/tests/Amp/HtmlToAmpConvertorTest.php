<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Amp;

use Symplify\Statie\Amp\HtmlToAmpConvertor;
use Symplify\Statie\Exception\Amp\NonHtmlFileException;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class HtmlToAmpConvertorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var HtmlToAmpConvertor
     */
    private $htmlToAmpConvertor;

    protected function setUp(): void
    {
        $this->htmlToAmpConvertor = $this->container->get(HtmlToAmpConvertor::class);
    }

    public function test(): void
    {
        $ampHtml = $this->htmlToAmpConvertor->convert(
            file_get_contents(__DIR__ . '/HtmlToAmpConvertorSource/file.html'),
            'https://original.com/url.html'
        );

        $this->assertContains('<html amp>', $ampHtml);
        $this->assertContains('<link rel="canonical" href="https://original.com/url.html">', $ampHtml);
    }

    public function testNonHtmlFile(): void
    {
        $this->expectException(NonHtmlFileException::class);
        $this->htmlToAmpConvertor->convert('someHtml', 'originalUrl.rss');
    }
}
