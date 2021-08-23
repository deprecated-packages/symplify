<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Twig\DummyService;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

final class DummyUrlGenerator implements UrlGeneratorInterface
{
    private RequestContext $context;

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH)
    {
        return 'some_dummy_url';
    }
}
