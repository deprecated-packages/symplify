<?php

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipEdgeCaseWithAssert extends TestCase
{
    public function testThat()
    {
        /** @var ErrorResource $response */
        $this->assertEquals('....', $response->getResponse()['message']);
    }
}
