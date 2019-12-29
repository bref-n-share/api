<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191229130821 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creates donor_site table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE donor_site (donor_id UUID NOT NULL, site_id UUID NOT NULL, PRIMARY KEY(donor_id, site_id))');
        $this->addSql('CREATE INDEX IDX_B46D2EA43DD7B7A7 ON donor_site (donor_id)');
        $this->addSql('CREATE INDEX IDX_B46D2EA4F6BD1646 ON donor_site (site_id)');
        $this->addSql('COMMENT ON COLUMN donor_site.donor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN donor_site.site_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE donor_site ADD CONSTRAINT FK_B46D2EA43DD7B7A7 FOREIGN KEY (donor_id) REFERENCES user_account (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE donor_site ADD CONSTRAINT FK_B46D2EA4F6BD1646 FOREIGN KEY (site_id) REFERENCES structure (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE donor_site');
    }
}
