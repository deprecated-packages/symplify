<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use Nette\Utils\Strings;
use PHPStan\Analyser\Scope;
use Symfony\Component\Finder\Finder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

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
    private array $latteUsedComponentNames = [];

    /**
     * @var string[]
     */
    private array $layoutUsedComponentNames = [];

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private FinderSanitizer $finderSanitizer
    ) {
    }

    /**
     * @todo should be scoped per template that is related to control/presenter
     *
     * @return string[]
     */
    public function resolveControlNames(Scope $scope): array
    {
        $suffixlessPresenterShortName = $this->resolveSuffixlessPresenterShortName($scope);
        if ($suffixlessPresenterShortName === null) {
            return [];
        }

        if (isset($this->latteUsedComponentNames[$suffixlessPresenterShortName])) {
            return $this->latteUsedComponentNames[$suffixlessPresenterShortName];
        }

        $latteFileInfos = $this->findLatteFileInfos($suffixlessPresenterShortName);

        $latteUsedComponentNames = $this->resolveControlNamesFromFileInfos($latteFileInfos);
        $this->latteUsedComponentNames[$suffixlessPresenterShortName] = $latteUsedComponentNames;

        return $latteUsedComponentNames;
    }

    /**
     * @return string[]
     */
    public function resolveLayoutControlNames(): array
    {
        if ($this->layoutUsedComponentNames !== []) {
            return $this->layoutUsedComponentNames;
        }

        $layoutLatteFileInfos = $this->findLatteLayoutFileInfos();
        $latteUsedComponentNames = $this->resolveControlNamesFromFileInfos($layoutLatteFileInfos);

        $this->layoutUsedComponentNames = $latteUsedComponentNames;

        return $latteUsedComponentNames;
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findLatteFileInfos(string $presenterPathName): array
    {
        $finder = new Finder();
        $finder->files()
            ->in(\getcwd())
            ->exclude('vendor')
            ->path($presenterPathName)
            ->name('*latte');

        return $this->finderSanitizer->sanitize($finder);
    }

    private function resolveSuffixlessPresenterShortName(Scope $scope): ?string
    {
        $shortClassName = $this->simpleNameResolver->resolveShortNameFromScope($scope);
        if ($shortClassName === null) {
            return null;
        }

        if (\str_ends_with($shortClassName, 'Presenter')) {
            return Strings::substring($shortClassName, 0, -Strings::length('Presenter'));
        }

        return $shortClassName;
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findLatteLayoutFileInfos(): array
    {
        $finder = new Finder();
        $finder->files()
            ->in(\getcwd())
            ->exclude('vendor')
            ->name('#@(.*?)\.latte$#');

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @param SmartFileInfo[] $latteFileInfos
     * @return string[]
     */
    private function resolveControlNamesFromFileInfos(array $latteFileInfos): array
    {
        $latteUsedComponentNames = [];

        foreach ($latteFileInfos as $latteFileInfo) {
            $matches = Strings::matchAll($latteFileInfo->getContents(), self::CONTROL_MARCO_REGEX);
            foreach ($matches as $match) {
                $latteUsedComponentNames[] = (string) $match[self::NAME_PART];
            }
        }

        return $latteUsedComponentNames;
    }
}
