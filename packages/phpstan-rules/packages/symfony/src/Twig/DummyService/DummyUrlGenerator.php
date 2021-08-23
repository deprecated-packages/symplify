<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Twig\DummyService;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

final class DummyUrlGenerator implements UrlGeneratorInterface
{
    private RequestContext $requestContext;

    public function setContext(RequestContext $requestContext): void
    {
        $this->requestContext = $requestContext;
    }

    public function getContext(): RequestContext
    {
        return $this->requestContext;
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        return 'some_dummy_url';
    }
}
