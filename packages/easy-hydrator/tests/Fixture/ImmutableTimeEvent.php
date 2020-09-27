<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests\Fixture;

use DateTimeImmutable;

final class ImmutableTimeEvent
{
    /**
     * @var DateTimeImmutable
     */
    private $when;

    public function __construct(DateTimeImmutable $when)
    {
        $this->when = $when;
    }

    public function getWhen(): DateTimeImmutable
    {
        return $this->when;
    }
}
