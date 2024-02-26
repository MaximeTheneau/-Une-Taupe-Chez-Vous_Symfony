<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240224162126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE list_posts CHANGE link_subtitle link_subtitle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE posts ADD img_whidth INT DEFAULT NULL, ADD img_height INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE list_posts CHANGE link_subtitle link_subtitle VARCHAR(250) DEFAULT NULL');
        $this->addSql('ALTER TABLE posts DROP img_whidth, DROP img_height');
    }
}
