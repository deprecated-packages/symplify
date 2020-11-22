<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\NodeFactory;

use PhpParser\Builder\Method;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;

final class GetNameClassMethodFactory
{
    public function create(): ClassMethod
    {
        $method = new Method('getName');
        $method->makePublic();
        $method->setReturnType('string');

        $classConstFetch = new ClassConstFetch(new Name('self'), 'FILTER_NAME');
        $return = new Return_($classConstFetch);

        $method->addStmt($return);

        return $method->getNode();
    }
}
