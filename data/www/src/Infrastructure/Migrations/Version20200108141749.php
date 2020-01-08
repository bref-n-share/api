<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200108141749 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE site DROP CONSTRAINT fk_694309e49e6b1585');
        $this->addSql('CREATE TABLE organization (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN organization.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637CBF396750 FOREIGN KEY (id) REFERENCES structure (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE organisation');
        $this->addSql('DROP INDEX idx_694309e49e6b1585');
        $this->addSql('ALTER TABLE site RENAME COLUMN organisation_id TO organization_id');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT FK_694309E432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_694309E432C8A3DE ON site (organization_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE site DROP CONSTRAINT FK_694309E432C8A3DE');
        $this->addSql('CREATE TABLE organisation (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN organisation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT fk_e6e132b4bf396750 FOREIGN KEY (id) REFERENCES structure (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP INDEX IDX_694309E432C8A3DE');
        $this->addSql('ALTER TABLE site RENAME COLUMN organization_id TO organisation_id');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT fk_694309e49e6b1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_694309e49e6b1585 ON site (organisation_id)');
    }
}
