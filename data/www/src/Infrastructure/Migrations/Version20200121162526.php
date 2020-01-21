<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200121162526 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE notification (id UUID NOT NULL, site_id UUID NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expiration_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, discriminator VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF5476CAF6BD1646 ON notification (site_id)');
        $this->addSql('COMMENT ON COLUMN notification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notification.site_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE post_notification (id UUID NOT NULL, post_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_14690B194B89032C ON post_notification (post_id)');
        $this->addSql('COMMENT ON COLUMN post_notification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN post_notification.post_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE simple_notification (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN simple_notification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post_notification ADD CONSTRAINT FK_14690B194B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post_notification ADD CONSTRAINT FK_14690B19BF396750 FOREIGN KEY (id) REFERENCES notification (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE simple_notification ADD CONSTRAINT FK_45AB26EABF396750 FOREIGN KEY (id) REFERENCES notification (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE flash_news');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE post_notification DROP CONSTRAINT FK_14690B19BF396750');
        $this->addSql('ALTER TABLE simple_notification DROP CONSTRAINT FK_45AB26EABF396750');
        $this->addSql('CREATE TABLE flash_news (id UUID NOT NULL, site_id UUID NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expiration_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_26969703f6bd1646 ON flash_news (site_id)');
        $this->addSql('COMMENT ON COLUMN flash_news.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN flash_news.site_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE flash_news ADD CONSTRAINT fk_26969703f6bd1646 FOREIGN KEY (site_id) REFERENCES site (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE post_notification');
        $this->addSql('DROP TABLE simple_notification');
    }
}
