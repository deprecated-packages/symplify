<?php

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Fixture;

final class NextFixer
{
    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var int
     */
    private $startBracketIndex;

    /**
     * @var int
     */
    private $endBracketIndex;

    /**
     * Static cache
     *
     * @var string[][]
     */
    private static $parentInterfacesPerInterface = [];

    /**
     * @var string[]
     */
    private $classTypes = [];

    /**
     * @var mixed[]
     *
     * Rich information about methods, e.g.:
     *
     *  0 => array (6)
     *     |  start => 18
     *     |  visibility => "public" (6)
     *     |  static => FALSE
     *     |  type => "method" (6)
     *     |  name => "secondMethod" (12)
     *     |  end => 29
     */
    private $methodElements = [];

    /**
     * @var mixed[]
     */
    private $propertyElements = [];

    /**
     * @var mixed[]
     */
    private $classyElements = [];

    /**
     * @var PropertyAccessWrapperFactory
     */
    private $propertyAccessWrapperFactory;

    /**
     * @var MethodWrapperFactory
     */
    private $methodWrapperFactory;

    /**
     * @var DocBlockFinder
     */
    private $docBlockFinder;

    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var NameFactory
     */
    private $nameFactory;

    /**
     * @var Token
     */
    private $classToken;

    /**
     * @var TokensAnalyzer
     */
    private $tokensAnalyzer;

    /**
     * @var ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;

    /**
     * @var PropertyWrapperFactory
     */
    private $propertyWrapperFactory;
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Fixture;

final class NextFixer
{
    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var int
     */
    private $startBracketIndex;

    /**
     * @var int
     */
    private $endBracketIndex;

    /**
     * Static cache
     *
     * @var string[][]
     */
    private static $parentInterfacesPerInterface = [];

    /**
     * @var string[]
     */
    private $classTypes = [];

    /**
     * @var mixed[]
     *
     * Rich information about methods, e.g.:
     *
     *  0 => array (6)
     *     |  start => 18
     *     |  visibility => "public" (6)
     *     |  static => FALSE
     *     |  type => "method" (6)
     *     |  name => "secondMethod" (12)
     *     |  end => 29
     */
    private $methodElements = [];

    /**
     * @var mixed[]
     */
    private $propertyElements = [];

    /**
     * @var mixed[]
     */
    private $classyElements = [];

    /**
     * @var PropertyAccessWrapperFactory
     */
    private $propertyAccessWrapperFactory;

    /**
     * @var MethodWrapperFactory
     */
    private $methodWrapperFactory;

    /**
     * @var DocBlockFinder
     */
    private $docBlockFinder;

    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var NameFactory
     */
    private $nameFactory;

    /**
     * @var Token
     */
    private $classToken;

    /**
     * @var TokensAnalyzer
     */
    private $tokensAnalyzer;

    /**
     * @var ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;

    /**
     * @var PropertyWrapperFactory
     */
    private $propertyWrapperFactory;
}

?>
