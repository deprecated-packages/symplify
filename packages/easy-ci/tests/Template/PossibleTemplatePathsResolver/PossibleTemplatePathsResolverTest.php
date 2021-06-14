<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Template\PossibleTemplatePathsResolver;

use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\Template\TemplatePathsResolver;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class PossibleTemplatePathsResolverTest extends AbstractKernelTestCase
{
    private TemplatePathsResolver $templatePathsResolver;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);

        $this->templatePathsResolver = $this->getService(TemplatePathsResolver::class);
    }

    public function test(): void
    {
        $templatePaths = $this->templatePathsResolver->resolveFromDirectories([__DIR__ . '/../../SomeBundle']);
        $this->assertCount(1, $templatePaths);

        $this->assertSame(['@RealClass/FirstName/SecondName/template.html.twig'], $templatePaths);
    }
}
