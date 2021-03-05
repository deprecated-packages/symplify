<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory;

use Nette\Utils\Strings;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use Symplify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface;

/**
 * Hacking constants @solve better in the future now it's hardcoded very deep in yaml parser, so unable to detected:
 * https://github.com/symfony/symfony/blob/ba4d57bb5fc0e9a1b4f63ced66156296dea3687e/src/Symfony/Component/Yaml/Inline.php#L617
 *
 * @see https://github.com/symfony/symfony/pull/18626/files
 */
final class ConstantNodeFactory
{
    /**
     * @var YamlFileContentProviderInterface
     */
    private $yamlFileContentProvider;

    public function __construct(YamlFileContentProviderInterface $yamlFileContentProvider)
    {
        $this->yamlFileContentProvider = $yamlFileContentProvider;
    }

    /**
     * @return ConstFetch|ClassConstFetch|null
     */
    public function createConstantIfValue(string $value): ?Expr
    {
        if (Strings::contains($value, '::')) {
            [$class, $constant] = explode('::', $value);

            // not uppercase → probably not a constant
            if (strtoupper($constant) !== $constant) {
                return null;
            }

            return new ClassConstFetch(new FullyQualified($class), $constant);
        }

        $definedConstants = get_defined_constants();

        foreach (array_keys($definedConstants) as $constantName) {
            if ($value !== constant($constantName)) {
                continue;
            }

            $yamlContent = $this->yamlFileContentProvider->getYamlContent();
            $constantDefinitionPattern = '#' . preg_quote('!php/const', '#') . '(\s)+' . $constantName . '#';

            if (! Strings::match($yamlContent, $constantDefinitionPattern)) {
                continue;
            }

            return new ConstFetch(new Name($constantName));
        }

        return null;
    }
}
