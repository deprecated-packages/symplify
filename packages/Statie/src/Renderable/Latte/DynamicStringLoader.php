<?php

declare(strict_types=1);

namespace Symplify\Statie\Renderable\Latte;

use Latte\ILoader;
use RuntimeException;

/**
 * Inspired by @see \Latte\Loaders\StringLoader.
 */
final class DynamicStringLoader implements ILoader
{
    /**
     * @var array [name => content]
     */
    private $templates = [];

    public function addTemplate(string $name, string $content)
    {
        $this->templates[$name] = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent($name) : string
    {
        if (isset($this->templates[$name])) {
            return $this->templates[$name];
        }

        throw new RuntimeException(
            sprintf('Missing template "%s".', $name)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired($name, $time) : bool
    {
        // needed?
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferredName($name, $referringName) : string
    {
        // needed?
        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getUniqueId($name)
    {
        // needed?
        return $this->getContent($name);
    }
}
