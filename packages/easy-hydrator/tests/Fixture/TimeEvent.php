<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests\Fixture;

use DateTimeInterface;

final class TimeEvent
{
    /**
     * @var DateTimeInterface
     */
    private $when;

    public function __construct(DateTimeInterface $when)
    {
        $this->when = $when;
    }

    public function getWhen(): DateTimeInterface
    {
        return $this->when;
    }
}
