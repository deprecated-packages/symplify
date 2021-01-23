<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipAssignInMultiLoopWithConcat
{
    public function run()
    {
        foreach ($class->getMethods() as $classMethod) {
            foreach ($removedPropertyNames as $removedPropertyName) {
                // remove methods
                $setMethodName = 'set' . ucfirst($removedPropertyName);
                $getMethodName = 'get' . ucfirst($removedPropertyName);

                if ($this->isNames($classMethod, [$setMethodName, $getMethodName])) {
                    continue;
                }

                $this->removeNode($classMethod);
            }
        }
    }
}
