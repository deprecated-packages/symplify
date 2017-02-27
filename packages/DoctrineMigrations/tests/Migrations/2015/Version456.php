<?php declare(strict_types=1);
namespace Zenify\DoctrineMigrations\Tests\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version456 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE "product" ( "id" integer NOT NULL );');
    }

    public function down(Schema $schema): void
    {
    }
}
