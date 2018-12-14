<?php

class SomeClass
{
    /**
     * Returns parent node for this node.
     *
     * @param \Exception $e An \Exception instance
     */
    public function getParent(\Exception $e)
    {
    }

    /**
     * @return \Github\Client
     */
    protected function getClientMock(): \Github\Client
    {
        return new \Github\Client($this->getHttpMethodsMock());
    }
}

?>
-----
<?php

class SomeClass
{
    /**
     * Returns parent node for this node.
     */
    public function getParent(\Exception $e)
    {
    }

    protected function getClientMock(): \Github\Client
    {
        return new \Github\Client($this->getHttpMethodsMock());
    }
}

?>
