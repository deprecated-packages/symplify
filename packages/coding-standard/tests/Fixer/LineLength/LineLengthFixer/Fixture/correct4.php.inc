<?php

declare(strict_types=1);

class SomeClass
{
    public function someFunction()
    {
        $this->assertSame([
            'one' => 1,
            'two' => 2,
        ], $parameterProvider->provide());

        $this->easyCodingStandardStyle->error(sprintf(
            'Found %d error%s.',
            $errorCount,
            $errorCount === 1 ? '' : 's'
        ));
    }
}
