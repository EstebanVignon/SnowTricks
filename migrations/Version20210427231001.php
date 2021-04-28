<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210427231001 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE esvi_comment (id VARCHAR(255) NOT NULL, trick_id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_226D47DDB281BE2E (trick_id), INDEX IDX_226D47DDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE esvi_comment ADD CONSTRAINT FK_226D47DDB281BE2E FOREIGN KEY (trick_id) REFERENCES esvi_trick (id)');
        $this->addSql('ALTER TABLE esvi_comment ADD CONSTRAINT FK_226D47DDA76ED395 FOREIGN KEY (user_id) REFERENCES esvi_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE esvi_comment');
    }
}
