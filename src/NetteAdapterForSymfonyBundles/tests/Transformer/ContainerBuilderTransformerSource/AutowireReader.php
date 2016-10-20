<?php

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\Transformer\ContainerBuilderTransformerSource;

use Doctrine\Common\Annotations\Reader;

class AutowireReader
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }
}
