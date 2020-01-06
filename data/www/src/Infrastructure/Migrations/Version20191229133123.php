<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191229133123 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creates FlashNews table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE flash_news (id UUID NOT NULL, site_id UUID NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expiration_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, current_place VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_26969703F6BD1646 ON flash_news (site_id)');
        $this->addSql('COMMENT ON COLUMN flash_news.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN flash_news.site_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE flash_news ADD CONSTRAINT FK_26969703F6BD1646 FOREIGN KEY (site_id) REFERENCES structure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE flash_news');
    }
}
