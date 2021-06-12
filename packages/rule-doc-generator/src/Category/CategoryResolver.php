<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Category;

use Symplify\RuleDocGenerator\Contract\Category\CategoryInfererInterface;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class CategoryResolver
{
    /**
     * @var string
     */
    private const CATEGORY_UNKNOWN = 'unknown';

    /**
     * @param CategoryInfererInterface[] $categoryInferers
     */
    public function __construct(
        private array $categoryInferers
    ) {
    }

    public function resolve(RuleDefinition $ruleDefinition): string
    {
        foreach ($this->categoryInferers as $categoryInferer) {
            $category = $categoryInferer->infer($ruleDefinition);
            if ($category) {
                return $category;
            }
        }

        return self::CATEGORY_UNKNOWN;
    }
}
