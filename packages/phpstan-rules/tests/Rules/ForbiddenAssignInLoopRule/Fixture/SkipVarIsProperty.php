<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipVarIsProperty
{
    public function run()
    {
        foreach ($parentNestingBreakTypes as $parentNestingBreakType) {
            if (! is_a($node, $parentNestingBreakType, true)) {
                continue;
            }

            $this->isBreakingNodeFoundFirst = true;
            return true;
        }

        foreach ($usedTraits as $usedTrait) {
            foreach ($this->traitsToRemove as $traitToRemove) {
                if ($this->isName($usedTrait, $traitToRemove)) {
                    $this->removeNode($usedTrait);
                    $this->classHasChanged = true;
                    continue 2;
                }
            }
        }
    }
}