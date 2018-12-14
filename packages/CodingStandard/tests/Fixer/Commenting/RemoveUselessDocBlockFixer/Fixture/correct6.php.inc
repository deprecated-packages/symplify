<?php
namespace SomeNamespace;

class SomeClass
{
    /**
     * @param Types\Context $context The context in which the DocBlock occurs.
     * @param DocBlock\Tags\Formatter $tagFormatter A custom tag formatter, defaults to PassthroughFormatter.
     */
    public function __construct(
        Types\Context $context = null,
        DocBlock\Tags\Formatter $tagFormatter = null
    ) {
    }
}
