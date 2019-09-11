<?php

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\AnnotationTypeExistsSniff\Wrong;

class SomeClass
{
    /**
     * @param NonExistingClass $param
     */
    public function someParam($param)
    {
    }
}
