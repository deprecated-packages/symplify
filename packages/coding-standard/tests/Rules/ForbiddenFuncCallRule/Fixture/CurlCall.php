<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenFuncCallRule\Fixture;

final class CurlCall
{
    public function run($url)
    {
        curl_init($url);
    }
}
