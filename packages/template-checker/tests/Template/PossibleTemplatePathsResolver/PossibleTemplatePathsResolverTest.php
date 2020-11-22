<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Tests\Template\PossibleTemplatePathsResolver;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\TemplateChecker\HttpKernel\TemplateCheckerKernel;
use Symplify\TemplateChecker\Template\TemplatePathsResolver;

final class PossibleTemplatePathsResolverTest extends AbstractKernelTestCase
{
    /**
     * @var TemplatePathsResolver
     */
    private $templatePathsResolver;

    protected function setUp(): void
    {
        self::bootKernel(TemplateCheckerKernel::class);

        $this->templatePathsResolver = self::$container->get(TemplatePathsResolver::class);
    }

    public function test(): void
    {
        $templatePaths = $this->templatePathsResolver->resolveFromDirectories([__DIR__ . '/../../SomeBundle']);
        $this->assertCount(1, $templatePaths);

        $this->assertSame(['@RealClass/FirstName/SecondName/template.html.twig'], $templatePaths);
    }
}
