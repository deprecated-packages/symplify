<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Templating\NodeFactory;

use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\Nop;
use Symplify\LattePHPStanCompiler\ValueObject\VariableAndType;

final class VarDocNodeFactory
{
    /**
     * @param VariableAndType[] $variablesAndTypes
     * @return Nop[]
     */
    public function createDocNodes(array $variablesAndTypes): array
    {
        $docNodes = [];
        foreach ($variablesAndTypes as $variableAndType) {
            $docNodes[] = $this->createDocNop($variableAndType);
        }

        return $docNodes;
    }

    private function createDocNop(VariableAndType $variableAndType): Nop
    {
        $prependVarTypesDocBlocks = sprintf(
            '/** @var %s $%s */',
            $variableAndType->getTypeAsString(),
            $variableAndType->getVariable()
        );

        // doc types node
        $docNop = new Nop();
        $docNop->setDocComment(new Doc($prependVarTypesDocBlocks));

        return $docNop;
    }
}
