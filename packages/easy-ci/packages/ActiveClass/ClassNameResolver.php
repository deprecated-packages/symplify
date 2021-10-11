<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassNameResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/t0IMqu/1
     */
    private const NAMESPACE_REGEX = '#^namespace (?<namespace>.*?);$#m';

    /**
     * @var string
     * @see https://regex101.com/r/NoGueg/1
     */
    private const CLASS_SHORT_NAME_REGEX = '#^(final ?)(class|interface|trait) (?<class_like_name>[\w_]+)#ms';

    public function resolveFromFromFileInfo(SmartFileInfo $phpFileInfo): ?string
    {
        // get class name
        $namespaceMatch = Strings::match($phpFileInfo->getContents(), self::NAMESPACE_REGEX);
        if (! isset($namespaceMatch['namespace'])) {
            return null;
        }

        $classLikeMatch = Strings::match($phpFileInfo->getContents(), self::CLASS_SHORT_NAME_REGEX);
        if (! isset($classLikeMatch['class_like_name'])) {
            return null;
        }

        return $namespaceMatch['namespace'] . '\\' . $classLikeMatch['class_like_name'];
    }
}
