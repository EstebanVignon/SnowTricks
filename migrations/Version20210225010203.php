<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210225010203 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE esvi_category (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE esvi_trick ADD category_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE esvi_trick ADD CONSTRAINT FK_8A712D5212469DE2 FOREIGN KEY (category_id) REFERENCES esvi_category (id)');
        $this->addSql('CREATE INDEX IDX_8A712D5212469DE2 ON esvi_trick (category_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE esvi_trick DROP FOREIGN KEY FK_8A712D5212469DE2');
        $this->addSql('DROP TABLE esvi_category');
        $this->addSql('DROP INDEX IDX_8A712D5212469DE2 ON esvi_trick');
        $this->addSql('ALTER TABLE esvi_trick DROP category_id');
    }
}
