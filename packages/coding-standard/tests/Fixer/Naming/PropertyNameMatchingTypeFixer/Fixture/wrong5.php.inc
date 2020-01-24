<?php

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

?>
-----
<?php

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

?>
