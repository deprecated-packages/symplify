<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree\Resolver;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Configuration\Category;

final class CategoryResolver
{
    /**
     * @var string
     */
    private const ADDED_PATTERN = '#\b(add(s|ed|ing)?)\b#i';

    /**
     * @var string
     */
    private const FIXED_PATTERN = '#\b(fix(es|ed|ing)?)\b#i';

    /**
     * @var string
     */
    private const REMOVED_PATTERN = '#\b(remov(e|es|ed|ing)|delet(e|es|ed|ing|)|drop(s|ped|ping)?)\b#i';

    /**
     * @var string
     */
    private const DEPRECATED_PATTERN = '#\b(deprecat(e|es|ed|ing))\b#i';

    public function resolveCategory(string $message): string
    {
        if (Strings::match($message, self::ADDED_PATTERN)) {
            return Category::ADDED;
        }

        if (Strings::match($message, self::FIXED_PATTERN)) {
            return Category::FIXED;
        }

        if (Strings::match($message, self::DEPRECATED_PATTERN)) {
            return Category::DEPRECATED;
        }

        if (Strings::match($message, self::REMOVED_PATTERN)) {
            return Category::REMOVED;
        }

        return Category::CHANGED;
    }
}
