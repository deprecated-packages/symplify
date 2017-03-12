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

    protected function setUp()
    {
        $this->messageAnalyzer = new MessageAnalyzer;
    }

    public function test()
    {
        $originalMessage = 'layout.footer';
        [$domain, $message] = $this->messageAnalyzer->extractDomainFromMessage($originalMessage);

        $this->assertSame('layout', $domain);
        $this->assertSame('footer', $message);
    }

    /**
     * @expectedException \Symplify\Statie\Translation\Exception\IncorrectTranslationFormatException
     */
    public function testIncorrectFormat()
    {
        $this->messageAnalyzer->extractDomainFromMessage('Some message');
    }
}
