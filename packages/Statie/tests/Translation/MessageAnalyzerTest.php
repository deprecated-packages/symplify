<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Translation;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Translation\MessageAnalyzer;

final class MessageAnalyzerTest extends TestCase
{
    /**
     * @var MessageAnalyzer
     */
    private $messageAnalyzer;

    protected function setUp(): void
    {
        $this->messageAnalyzer = new MessageAnalyzer;
    }

    public function test(): void
    {
        $originalMessage = 'layout.footer';
        [$domain, $message] = $this->messageAnalyzer->extractDomainFromMessage($originalMessage);

        $this->assertSame('layout', $domain);
        $this->assertSame('footer', $message);
    }

    /**
     * @expectedException \Symplify\Statie\Translation\Exception\IncorrectTranslationFormatException
     */
    public function testIncorrectFormat(): void
    {
        $this->messageAnalyzer->extractDomainFromMessage('Some message');
    }
}
