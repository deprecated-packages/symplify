<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Twig\DummyService;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

final class DummyUrlGenerator implements UrlGeneratorInterface
{
<<<<<<< HEAD
    private RequestContext $requestContext;

    public function setContext(RequestContext $requestContext): void
    {
        $this->requestContext = $requestContext;
=======
    private RequestContext $context;

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
>>>>>>> 45db8b8e4 ([PHPStanRules] Add twig + path etension for easier parsing)
    }

    public function getContext(): RequestContext
    {
<<<<<<< HEAD
        return $this->requestContext;
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
=======
        return $this->context;
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH)
>>>>>>> 45db8b8e4 ([PHPStanRules] Add twig + path etension for easier parsing)
    {
        return 'some_dummy_url';
    }
}
