<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules\DocBlock;

use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Generics\GenericObjectTypeCheck;
use PHPStan\Rules\Rule;
use PHPStan\Type\FileTypeMapper;

/**
 * Inspirtion see https://github.com/phpstan/phpstan-src/blob/master/src/Rules/PhpDoc/IncompatiblePhpDocTypeRule.php
 */
final class NoNonExistingVarParamReturnThrowRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Some error';

    /** @var FileTypeMapper */
    private $fileTypeMapper;

    /** @var \PHPStan\Rules\Generics\GenericObjectTypeCheck */
    private $genericObjectTypeCheck;

    public function __construct(
        FileTypeMapper $fileTypeMapper,
        GenericObjectTypeCheck $genericObjectTypeCheck
    )
    {
        $this->fileTypeMapper = $fileTypeMapper;
        $this->genericObjectTypeCheck = $genericObjectTypeCheck;
    }

    /**
     * @return string[]
     */
    public function getNodeType(): string
    {
        return Property::class;
    }

    /**
     * @param Property $node
     * @param Scope $scope
     * @return array
     */
    public function processNode(\PhpParser\Node $node, Scope $scope): array
    {
        $docComment = $node->getDocComment();
        if ($docComment === null) {
            return [];
        }


        die;
    }
}
