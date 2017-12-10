<?php declare(strict_types=1);

class SomeClass
{
    /**
     * @var \Symplify\GitWrapper\GitWrapper
     */
    protected $wrapper;

    public function __construct(GitWrapper $wrapper)
    {
        $this->wrapper = $wrapper;
    }
}
