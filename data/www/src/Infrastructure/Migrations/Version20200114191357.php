<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200114191357 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE information (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN information.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE request (id UUID NOT NULL, category_id UUID NOT NULL, requested_quantity INT DEFAULT NULL, current_quantity INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3B978F9F12469DE2 ON request (category_id)');
        $this->addSql('COMMENT ON COLUMN request.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN request.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE information ADD CONSTRAINT FK_29791883BF396750 FOREIGN KEY (id) REFERENCES post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FBF396750 FOREIGN KEY (id) REFERENCES post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT fk_5a8a6c8d12469de2');
        $this->addSql('DROP INDEX idx_5a8a6c8d12469de2');
        $this->addSql('ALTER TABLE post DROP category_id');
        $this->addSql('ALTER TABLE post DROP requested_quantity');
        $this->addSql('ALTER TABLE post DROP current_quantity');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE information');
        $this->addSql('DROP TABLE request');
        $this->addSql('ALTER TABLE post ADD category_id UUID NOT NULL');
        $this->addSql('ALTER TABLE post ADD requested_quantity INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD current_quantity INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN post.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT fk_5a8a6c8d12469de2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5a8a6c8d12469de2 ON post (category_id)');
    }
}
