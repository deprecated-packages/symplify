<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Composer;

use Composer\Autoload\ClassLoader;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class ClassLoaderDecorator
{
    /**
     * @var StandardFinder
     */
    private $standardFinder;

    public function __construct(StandardFinder $standardFinder)
    {
        $this->standardFinder = $standardFinder;
    }

    public function decorate(ClassLoader $classLoader)
    {
        $standards = $this->standardFinder->getStandards();
        foreach ($standards as $standardName => $standardRuleset) {
            if ($this->isDefaultStandard($standardName)) {
                continue;
            }

            $standardNamespace = $this->detectStandardNamespaceFromStandardName($standardName);
            $standardDir = dirname($standardRuleset);

            $classLoader->addPsr4(
                $standardNamespace . '\\',
                $standardDir . DIRECTORY_SEPARATOR . $standardNamespace
            );
        }
    }

    private function isDefaultStandard(string $standardName) : bool
    {
        return in_array(
            $standardName,
            ['PSR1', 'MySource', 'PSR2', 'Zend', 'PEAR', 'Squiz', 'Generic'],
            true
        );
    }

    private function detectStandardNamespaceFromStandardName(string $standardName) : string
    {
        return str_replace(' ', '', $standardName);
    }
}
