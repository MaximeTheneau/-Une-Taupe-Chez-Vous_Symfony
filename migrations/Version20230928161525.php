<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230928161525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paragraph_posts_posts (paragraph_posts_id INT NOT NULL, posts_id INT NOT NULL, INDEX IDX_7690A15B58451C04 (paragraph_posts_id), INDEX IDX_7690A15BD5E258C5 (posts_id), PRIMARY KEY(paragraph_posts_id, posts_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE paragraph_posts_posts ADD CONSTRAINT FK_7690A15B58451C04 FOREIGN KEY (paragraph_posts_id) REFERENCES paragraph_posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paragraph_posts_posts ADD CONSTRAINT FK_7690A15BD5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paragraph_posts ADD link_subtitle VARCHAR(255) DEFAULT NULL, CHANGE link link VARCHAR(500) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paragraph_posts_posts DROP FOREIGN KEY FK_7690A15B58451C04');
        $this->addSql('ALTER TABLE paragraph_posts_posts DROP FOREIGN KEY FK_7690A15BD5E258C5');
        $this->addSql('DROP TABLE paragraph_posts_posts');
        $this->addSql('ALTER TABLE paragraph_posts DROP link_subtitle, CHANGE link link VARCHAR(255) DEFAULT NULL');
    }
}
