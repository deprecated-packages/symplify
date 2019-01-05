<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Loader;

use Latte\ILoader;
use Nette\Utils\ObjectHelpers;
use Nette\Utils\Strings;
use Symplify\Statie\Latte\Exception\MissingLatteTemplateException;
use function Safe\sprintf;

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
        if (isset($this->templates[$name])) {
            return $this->templates[$name];
        }

        $message = sprintf(
            'Missing template "%s". It must be placed in "/_layouts" or "/_snippets" and full relative path have to be used, e.g. "_layouts/default.twig".',
            $name
        );

        // add suggestsion
        $suggestion = ObjectHelpers::getSuggestion($this->getLayoutAndSnippetNames(), $name);
        if ($suggestion) {
            $message .= sprintf(' Did you mean "%s"?', $suggestion);
        } elseif ($this->getLayoutAndSnippetNames()) {
            $message .= PHP_EOL . PHP_EOL . sprintf(
                'Pick one of these: "%s"',
                implode('", "', $this->getLayoutAndSnippetNames())
            );
        }

        throw new MissingLatteTemplateException($message);
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

    /**
     * @return string[]
     */
    private function getLayoutAndSnippetNames(): array
    {
        $layoutAndSnippetNames = [];
        foreach (array_keys($this->templates) as $name) {
            if (! Strings::match($name, '#(_layouts|_snippets)#')) {
                continue;
            }

            $layoutAndSnippetNames[] = $name;
        }

        return $layoutAndSnippetNames;
    }
}
