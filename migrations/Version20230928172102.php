<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230928172102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paragraph_posts DROP FOREIGN KEY paragraph_posts_ibfk_1');
        $this->addSql('DROP INDEX link_post_select ON paragraph_posts');
        $this->addSql('ALTER TABLE paragraph_posts CHANGE link_post_select link_post_select VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paragraph_posts CHANGE link_post_select link_post_select INT DEFAULT NULL');
        $this->addSql('ALTER TABLE paragraph_posts ADD CONSTRAINT paragraph_posts_ibfk_1 FOREIGN KEY (link_post_select) REFERENCES posts (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX link_post_select ON paragraph_posts (link_post_select)');
    }
}
