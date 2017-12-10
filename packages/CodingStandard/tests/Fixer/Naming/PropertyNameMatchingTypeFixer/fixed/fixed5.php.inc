<?php declare(strict_types=1);

class SomeClass
{
    /**
     * @var \Symplify\GitWrapper\GitWrapper
     */
    protected $gitWrapper;

    public function __construct(GitWrapper $gitWrapper)
    {
        $this->gitWrapper = $gitWrapper;
    }
}
