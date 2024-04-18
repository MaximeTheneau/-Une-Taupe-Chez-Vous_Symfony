<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418091519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE paragraph_posts ADD img_post VARCHAR(500) DEFAULT NULL, ADD srcset LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE posts ADD srcset LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paragraph_posts DROP img_post, DROP srcset');
        $this->addSql('ALTER TABLE comments CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE posts DROP srcset');
    }
}
