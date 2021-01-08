<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoSetterOnServiceRule\Fixture\Service;

final class SkipSomeServiceWithPrivateSetter
{
    private $option;

    public function __construct(stdClass $option)
    {
        $this->setOption($option);
    }

    private function setOption(stdClass $option)
    {
        $this->option = $option;
    }
}
