<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\HttpServer\MimeType;

use Mimey\MimeTypes;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_Sculpin\HttpServer\MimeType\MimeTypeDetector;

final class MimeTypeDetectorTest extends TestCase
{
    public function test()
    {
        $mimeTypeDetector = new MimeTypeDetector(new MimeTypes());
        $this->assertSame('text/html', $mimeTypeDetector->detectForFilename('some.html'));
        $this->assertSame('text/plain', $mimeTypeDetector->detectForFilename('some.txt'));
        $this->assertSame('application/rss+xml', $mimeTypeDetector->detectForFilename('some.rss'));
        $this->assertSame('application/octet-stream', $mimeTypeDetector->detectForFilename('blah'));
    }
}
