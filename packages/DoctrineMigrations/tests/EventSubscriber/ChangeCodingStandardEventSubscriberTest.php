<?php declare(strict_types=1);

namespace Zenify\DoctrineMigrations\Tests\EventSubscriber;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ChangeCodingStandardEventSubscriberTest extends AbstractEventSubscriberTest
{
    protected function setUp()
    {
        parent::setUp();

        /** @var Configuration $configuration */
        $configuration = $this->container->getByType(Configuration::class);
        $configuration->setMigrationsDirectory($this->getMigrationsDirectory());
    }

    public function testDispatchingGenerateCommand()
    {
        $input = new ArrayInput(['command' => 'migrations:generate']);
        $output = new BufferedOutput;

        $result = $this->application->run($input, $output);
        $this->assertSame(0, $result);
        $this->assertCommandOutputAndMigrationCodeStyle($output->fetch());
    }

    public function testDispatchingDiffCommand()
    {
        $input = new ArrayInput(['command' => 'migrations:diff']);
        $output = new BufferedOutput;

        $result = $this->application->run($input, $output);
        $this->assertSame(0, $result);
        $this->assertCommandOutputAndMigrationCodeStyle($output->fetch());
    }

    /**
     * @param string $outputContent
     */
    private function assertCommandOutputAndMigrationCodeStyle($outputContent)
    {
        $this->assertContains('Generated new migration class to', $outputContent);

        $migrationFile = $this->extractMigrationFile($outputContent);
        $fileContents = file_get_contents($migrationFile);
        $this->assertNotContains('    ', $fileContents);
        $this->assertContains("\t", $fileContents);
    }

    /**
     * @return string
     */
    private function getMigrationsDirectory()
    {
        $migrationsDirectory = sys_get_temp_dir() . '/doctrine-migrations/Migrations';
        FileSystem::createDir($migrationsDirectory);
        return $migrationsDirectory;
    }

    /**
     * @param string $outputContent
     * @return string
     */
    private function extractMigrationFile($outputContent)
    {
        preg_match('/"([^"]+)"/', $outputContent, $matches);
        return $matches[1];
    }
}
