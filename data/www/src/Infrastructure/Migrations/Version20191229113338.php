<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191229113338 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creates Structure table';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE structure (id UUID NOT NULL, organisation_id UUID NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, postal_code VARCHAR(5) NOT NULL, city VARCHAR(255) NOT NULL, current_place VARCHAR(255) NOT NULL, discriminator VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6F0137EA9E6B1585 ON structure (organisation_id)');
        $this->addSql('COMMENT ON COLUMN structure.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN structure.organisation_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EA9E6B1585 FOREIGN KEY (organisation_id) REFERENCES structure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_account ADD structure_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN user_account.structure_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user_account ADD CONSTRAINT FK_8D93D6492534008B FOREIGN KEY (structure_id) REFERENCES structure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D6492534008B ON user_account (structure_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE user_account DROP CONSTRAINT FK_8D93D6492534008B');
        $this->addSql('ALTER TABLE structure DROP CONSTRAINT FK_6F0137EA9E6B1585');
        $this->addSql('DROP TABLE structure');
        $this->addSql('DROP INDEX IDX_8D93D6492534008B');
        $this->addSql('ALTER TABLE user_account DROP structure_id');
    }
}
