<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use Nette\Utils\Strings;
use PHPStan\Analyser\Scope;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\Astral\Naming\SimpleNameResolver;

final class LatteUsedControlResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/iTz04c/1/
     */
    private const CONTROL_MARCO_REGEX = '#{(control|form) (?<' . self::NAME_PART . '>\w+)(.*?)}#';

    /**
     * @var string
     */
    private const NAME_PART = 'name';

    /**
     * @var array<string, string[]>
     */
    private $latteUsedComponentNames = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @todo should be scoped per template that is related to control/presenter
     *
     * @return string[]
     */
    public function resolveControlMethodNames(Scope $scope): array
    {
        $shortClassName = $this->simpleNameResolver->resolveShortNameFromScope($scope);
        if ($shortClassName === null) {
            return [];
        }

        if (Strings::endsWith($shortClassName, 'Presenter')) {
            $shortClassName = Strings::substring($shortClassName, 0, - Strings::length('Presenter'));
        }

        if (isset($this->latteUsedComponentNames[$shortClassName])) {
            return $this->latteUsedComponentNames[$shortClassName];
        }

        $latteFileInfos = $this->findLatteFileInfos($shortClassName);

        $latteUsedComponentNames = [];
        foreach ($latteFileInfos as $latteFileInfo) {
            // @see https://regex101.com/r/sROkSZ/1/
            $matches = Strings::matchAll($latteFileInfo->getContents(), self::CONTROL_MARCO_REGEX);
            foreach ($matches as $match) {
                $latteUsedComponentNames[] = (string) $match[self::NAME_PART];
            }
        }

        $this->latteUsedComponentNames[$shortClassName] = $latteUsedComponentNames;

        return $latteUsedComponentNames;
    }

    /**
     * @return SplFileInfo[]
     */
    private function findLatteFileInfos(string $presenterPathName): array
    {
        $finder = new Finder();
        $finder->files()
            ->in(\getcwd())
            ->exclude('vendor')
            ->path($presenterPathName)
            ->name('*latte');

        return iterator_to_array($finder->getIterator());
    }
}
