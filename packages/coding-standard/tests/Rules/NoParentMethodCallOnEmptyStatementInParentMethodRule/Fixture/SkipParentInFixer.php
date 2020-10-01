<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule\Fixture;

use PhpCsFixer\AbstractDoctrineAnnotationFixer;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Doctrine\Annotation\Tokens as DoctrineAnnotationTokens;

final class SkipParentInFixer extends AbstractDoctrineAnnotationFixer
{
    /**
     * @var SomeObject
     */
    private $someObject;

    public function __construct(SomeObject $someObject)
    {

        $this->someObject = $someObject;

        parent::__construct();
    }


    protected function fixAnnotations(DoctrineAnnotationTokens $doctrineAnnotationTokens)
    {
    }

    public function getDefinition()
    {
    }
}
