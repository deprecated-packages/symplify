<?php declare(strict_types=1);

namespace Symplify\DoctrineMigrations\Tests\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symplify\DoctrineMigrations\Tests\Configuration\ConfigurationSource\SomeService;

final class Version123 extends AbstractMigration
{
    /**
     * @inject
     * @var SomeService
     */
    public $someService;

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE "category" ( "id" integer NOT NULL );');
    }

    public function down(Schema $schema): void
    {
    }
}
