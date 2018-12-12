<?php

class SomeClass
{
    /**
     * Returns parent node for this node.
     *
     * @return NodeInterface|null
     */
    public function getParent(): ?NodeInterface
    {
        return $this->parent;
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
    public function getParent(): ?NodeInterface
    {
        return $this->parent;
    }
}

?>
