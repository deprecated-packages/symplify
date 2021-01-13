<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\NamespaceDetector;

use PHPUnit\Framework\TestCase;
use Symplify\Autodiscovery\NamespaceDetector;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class NamespaceDetectorTest extends TestCase
{
    public function test(): void
    {
        $namespaceDetector = new NamespaceDetector(new SmartFileSystem());
        $directoryFileInfo = new SmartFileInfo(__DIR__ . '/Source');

        $resolvedNamespace = $namespaceDetector->detectFromDirectory($directoryFileInfo);
        $this->assertSame('Symplify\Autodiscovery\Tests\NamespaceDetector\Source', $resolvedNamespace);
    }
}
