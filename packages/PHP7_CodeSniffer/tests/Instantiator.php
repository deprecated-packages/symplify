<?php

namespace Symplify\PHP7_CodeSniffer\Tests;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Tests\Output\TestOutput;
use Symplify\PHP7_CodeSniffer\Application\Application;
use Symplify\PHP7_CodeSniffer\Application\FileProcessor;
use Symplify\PHP7_CodeSniffer\Application\Fixer;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\SniffsOptionResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\SourceOptionResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\StandardsOptionResolver;
use Symplify\PHP7_CodeSniffer\Console\Style\CodeSnifferStyle;
use Symplify\PHP7_CodeSniffer\EventDispatcher\CurrentListenerSniffCodeProvider;
use Symplify\PHP7_CodeSniffer\EventDispatcher\SniffDispatcher;
use Symplify\PHP7_CodeSniffer\File\FileFactory;
use Symplify\PHP7_CodeSniffer\File\Finder\SourceFinder;
use Symplify\PHP7_CodeSniffer\File\Provider\FilesProvider;
use Symplify\PHP7_CodeSniffer\Parser\EolCharDetector;
use Symplify\PHP7_CodeSniffer\Parser\FileToTokensParser;
use Symplify\PHP7_CodeSniffer\Report\ErrorDataCollector;
use Symplify\PHP7_CodeSniffer\Report\ErrorMessageSorter;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\RulesetXmlToSniffsFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\SingleSniffFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\SniffCodeToSniffsFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\StandardNameToSniffsFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Routing\Router;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassFilter;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassRobotLoaderFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Sniff\SniffSetFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\SniffPropertyValueDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\ExcludedSniffDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\Extractor\SniffPropertyValuesExtractor;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class Instantiator
{
    /**
     * @var RulesetXmlToSniffsFactory
     */
    private static $cachedRulesetXmlToSniffFactory;

    public static function createRulesetXmlToSniffsFactory() : RulesetXmlToSniffsFactory
    {
        $sniffSetFactory = self::createSniffSetFactory();
        $rulesetXmlToSniffsFactory = self::createBareRulesetXmlToSniffsFactory();
        $sniffSetFactory->addSniffFactory($rulesetXmlToSniffsFactory);

        return $rulesetXmlToSniffsFactory;
    }

    private static function createBareRulesetXmlToSniffsFactory() : RulesetXmlToSniffsFactory
    {
        if (self::$cachedRulesetXmlToSniffFactory) {
            return self::$cachedRulesetXmlToSniffFactory;
        }

        return self::$cachedRulesetXmlToSniffFactory = new RulesetXmlToSniffsFactory(
            self::createSniffFinder(),
            new ExcludedSniffDataCollector(),
            self::createSniffPropertyValueDataCollector(),
            self::createSingleSniffFactory()
        );
    }

    public static function createConfigurationResolver() : ConfigurationResolver
    {
        $configurationResolver = new ConfigurationResolver();
        $configurationResolver->addOptionResolver(new StandardsOptionResolver(
            new StandardFinder()
        ));
        $configurationResolver->addOptionResolver(new SniffsOptionResolver());
        $configurationResolver->addOptionResolver(new SourceOptionResolver());

        return $configurationResolver;
    }

    public static function createSniffFinder() : SniffFinder
    {
        return new SniffFinder(
            new SniffClassRobotLoaderFactory(),
            new SniffClassFilter()
        );
    }

    public static function createFileFactory() : FileFactory
    {
        return new FileFactory(
            new Fixer(),
            self::createErrorDataCollector(),
            new FileToTokensParser(new EolCharDetector()),
            new EolCharDetector()
        );
    }

    public static function createErrorDataCollector() : ErrorDataCollector
    {
        return new ErrorDataCollector(
            new CurrentListenerSniffCodeProvider(),
            new ErrorMessageSorter()
        );
    }

    public static function createApplication() : Application
    {
        return new Application(
            self::createSniffDispatcher(),
            new FilesProvider(new SourceFinder(), self::createFileFactory()),
            self::createSniffSetFactory(),
            new ExcludedSniffDataCollector(),
            self::createConfigurationResolver(),
            new FileProcessor(self::createSniffDispatcher(), new Fixer())
        );
    }

    public static function createCodeSnifferStyle() : CodeSnifferStyle
    {
        return new CodeSnifferStyle(
            new ArgvInput(),
            new TestOutput()
        );
    }

    public static function createRouter() : Router
    {
        return new Router(self::createSniffFinder());
    }

    public static function createSniffSetFactory(
        SingleSniffFactory $singleSniffFactory = null
    ) : SniffSetFactory {
        $sniffSetFactory = new SniffSetFactory(
            self::createConfigurationResolver()
        );

        $sniffSetFactory->addSniffFactory(new SniffCodeToSniffsFactory(
            self::createRouter(),
            $singleSniffFactory = $singleSniffFactory ?: self::createSingleSniffFactory()
        ));

        $sniffSetFactory->addSniffFactory(new StandardNameToSniffsFactory(
            new StandardFinder(),
            self::createBareRulesetXmlToSniffsFactory()
        ));

        $sniffSetFactory->addSniffFactory(
            self::createBareRulesetXmlToSniffsFactory()
        );

        return $sniffSetFactory;
    }

    public static function createSingleSniffFactory() : SingleSniffFactory
    {
        return new SingleSniffFactory(
            new ExcludedSniffDataCollector(),
            self::createSniffPropertyValueDataCollector()
        );
    }

    private static function createSniffDispatcher() : SniffDispatcher
    {
        return new SniffDispatcher(new CurrentListenerSniffCodeProvider());
    }

    public static function createSniffSetFactoryWithExcludedDataCollector(
        ExcludedSniffDataCollector $excludedSniffDataCollector
    ) : SniffSetFactory {
        $singleSniffFactory = new SingleSniffFactory(
            $excludedSniffDataCollector,
            self::createSniffPropertyValueDataCollector()
        );

        $sniffSetFactory = self::createSniffSetFactory($singleSniffFactory);

        return $sniffSetFactory;
    }

    public static function createSniffPropertyValueDataCollector() : SniffPropertyValueDataCollector
    {
        return new SniffPropertyValueDataCollector(
            new SniffPropertyValuesExtractor()
        );
    }
}
