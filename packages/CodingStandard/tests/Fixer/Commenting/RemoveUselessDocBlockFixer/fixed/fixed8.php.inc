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
