<?php declare(strict_types=1);

namespace Symplify\Statie\FlatWhite\Latte;

use Latte\ILoader;
use RuntimeException;
use Symplify\Statie\FlatWhite\Contract\Latte\MutableContentLoaderInterface;

/**
 * Inspired by @see \Latte\Loaders\StringLoader.
 */
final class DynamicStringLoader implements ILoader, MutableContentLoaderInterface
{
    /**
     * @var array [name => content]
     */
    private $templates = [];

    public function changeContent(string $name, string $content): void
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
        // not needed
        return false;
    }

    /**
     * @param string $name
     * @param string $referringName
     */
    public function getReferredName($name, $referringName): string
    {
        // not needed
        return $name;
    }

    /**
     * @param string $name
     */
    public function getUniqueId($name): string
    {
        // not needed
        return $this->getContent($name);
    }
}
