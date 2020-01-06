<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200106223714 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Renamed currentPlace to status';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE user_account RENAME COLUMN current_place TO status');
        $this->addSql('ALTER TABLE structure RENAME COLUMN current_place TO status');
        $this->addSql('ALTER TABLE post RENAME COLUMN current_place TO status');
        $this->addSql('ALTER TABLE flash_news RENAME COLUMN current_place TO status');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE user_account RENAME COLUMN status TO current_place');
        $this->addSql('ALTER TABLE structure RENAME COLUMN status TO current_place');
        $this->addSql('ALTER TABLE post RENAME COLUMN status TO current_place');
        $this->addSql('ALTER TABLE flash_news RENAME COLUMN status TO current_place');
    }
}
