<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass;

use Nette\Utils\Strings;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use Symplify\EasyCI\ActiveClass\NodeVisitor\ClassNameNodeVisitor;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\ActiveClass\ClassNameResolver\ClassNameResolverTest
 */
final class ClassNameResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/t0IMqu/1
     */
    private const NAMESPACE_REGEX = '#^namespace (?<' . self::NAMESPACE_PART . '>.*?);$#m';

    /**
     * @var string
     * @see https://regex101.com/r/NoGueg/1
     */
    private const CLASS_SHORT_NAME_REGEX = '#^(final ?)(class|interface|trait) (?<' . self::CLASS_LIKE_NAME_PART . '>[\w_]+)#ms';

    /**
     * @var string
     */
    private const CLASS_LIKE_NAME_PART = 'class_like_name';

    /**
     * @var string
     */
    private const NAMESPACE_PART = 'namespace';

    public function __construct(
        private Parser $parser
    ) {
    }

    public function resolveFromFromFileInfo(SmartFileInfo $phpFileInfo): ?string
    {
        $stmts = $this->parser->parse($phpFileInfo->getContents());

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->traverse($stmts);

        $classNameNodeVisitor = new ClassNameNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($classNameNodeVisitor);
        $nodeTraverser->traverse($stmts);

        dump($classNameNodeVisitor->getClassName());
        die;

        // get class name
        $namespaceMatch = Strings::match($phpFileInfo->getContents(), self::NAMESPACE_REGEX);
        if (! isset($namespaceMatch[self::NAMESPACE_PART])) {
            return null;
        }

        $classLikeMatch = Strings::match($phpFileInfo->getContents(), self::CLASS_SHORT_NAME_REGEX);
        if (! isset($classLikeMatch[self::CLASS_LIKE_NAME_PART])) {
            return null;
        }

        return $namespaceMatch[self::NAMESPACE_PART] . '\\' . $classLikeMatch[self::CLASS_LIKE_NAME_PART];
    }
}
