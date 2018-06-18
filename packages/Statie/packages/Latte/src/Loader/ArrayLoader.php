<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Loader;

use Latte\ILoader;
use Symplify\Statie\Latte\Exception\MissingLatteTemplateException;

/**
 * Inspired by @see \Latte\Loaders\StringLoader.
 */
final class ArrayLoader implements ILoader
{
    /**
     * @var string[]
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
        if ($this->templates === []) {
            // is content itself, not a reference to file
            return $name;
        }

        if (isset($this->templates[$name])) {
            return $this->templates[$name];
        }

        throw new MissingLatteTemplateException(sprintf(
            'Missing template "%s". Is it placed in "/_layouts" or "/_snippets" directory?',
            $name
        ));
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
