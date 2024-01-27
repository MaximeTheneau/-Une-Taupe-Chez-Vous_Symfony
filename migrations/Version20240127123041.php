<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240127123041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments_reply (id INT AUTO_INCREMENT NOT NULL, posts_id INT DEFAULT NULL, user VARCHAR(70) NOT NULL, email VARCHAR(255) NOT NULL, comment VARCHAR(2000) NOT NULL, accepted TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B59E2186D5E258C5 (posts_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments_reply ADD CONSTRAINT FK_B59E2186D5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments_reply DROP FOREIGN KEY FK_B59E2186D5E258C5');
        $this->addSql('DROP TABLE comments_reply');
    }
}
