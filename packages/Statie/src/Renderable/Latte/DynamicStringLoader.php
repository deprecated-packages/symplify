<?php declare(strict_types=1);

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

    public function addTemplate(string $name, string $content): void
    {
        $this->templates[$name] = $content;
    }

    /**
     * @param string $name
     */
    public function getContent($name): string
    {
        if (isset($this->templates[$name])) {
            return $this->templates[$name];
        }

        throw new RuntimeException(
            sprintf('Missing template "%s".', $name)
        );
    }

    /**
     * @param string $name
     * @param int $time
     */
    public function isExpired($name, $time): bool
    {
        // needed?
        return false;
    }

    /**
     * @param string $name
     * @param string $referringName
     */
    public function getReferredName($name, $referringName): string
    {
        // needed?
        return $name;
    }

    /**
     * @param string $name
     */
    public function getUniqueId($name): string
    {
        // needed?
        return $this->getContent($name);
    }
}
