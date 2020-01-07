<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200106173334 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE organisation (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN organisation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE site (id UUID NOT NULL, organisation_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_694309E49E6B1585 ON site (organisation_id)');
        $this->addSql('COMMENT ON COLUMN site.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN site.organisation_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B4BF396750 FOREIGN KEY (id) REFERENCES structure (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT FK_694309E49E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT FK_694309E4BF396750 FOREIGN KEY (id) REFERENCES structure (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX uniq_8d93d649e7927c74 RENAME TO UNIQ_253B48AEE7927C74');
        $this->addSql('ALTER INDEX idx_8d93d6492534008b RENAME TO IDX_253B48AE2534008B');
        $this->addSql('ALTER TABLE donor_site DROP CONSTRAINT FK_B46D2EA4F6BD1646');
        $this->addSql('ALTER TABLE donor_site ADD CONSTRAINT FK_B46D2EA4F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE structure DROP CONSTRAINT fk_6f0137ea9e6b1585');
        $this->addSql('DROP INDEX idx_6f0137ea9e6b1585');
        $this->addSql('ALTER TABLE structure DROP organisation_id');
        $this->addSql('ALTER TABLE flash_news DROP CONSTRAINT FK_26969703F6BD1646');
        $this->addSql('ALTER TABLE flash_news ADD CONSTRAINT FK_26969703F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE site DROP CONSTRAINT FK_694309E49E6B1585');
        $this->addSql('ALTER TABLE donor_site DROP CONSTRAINT FK_B46D2EA4F6BD1646');
        $this->addSql('ALTER TABLE flash_news DROP CONSTRAINT FK_26969703F6BD1646');
        $this->addSql('DROP TABLE organisation');
        $this->addSql('DROP TABLE site');
        $this->addSql('ALTER INDEX idx_253b48ae2534008b RENAME TO idx_8d93d6492534008b');
        $this->addSql('ALTER INDEX uniq_253b48aee7927c74 RENAME TO uniq_8d93d649e7927c74');
        $this->addSql('ALTER TABLE structure ADD organisation_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN structure.organisation_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT fk_6f0137ea9e6b1585 FOREIGN KEY (organisation_id) REFERENCES structure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6f0137ea9e6b1585 ON structure (organisation_id)');
        $this->addSql('ALTER TABLE donor_site DROP CONSTRAINT fk_b46d2ea4f6bd1646');
        $this->addSql('ALTER TABLE donor_site ADD CONSTRAINT fk_b46d2ea4f6bd1646 FOREIGN KEY (site_id) REFERENCES structure (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE flash_news DROP CONSTRAINT fk_26969703f6bd1646');
        $this->addSql('ALTER TABLE flash_news ADD CONSTRAINT fk_26969703f6bd1646 FOREIGN KEY (site_id) REFERENCES structure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
