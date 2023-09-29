<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230929103536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE keyword_posts (keyword_id INT NOT NULL, posts_id INT NOT NULL, INDEX IDX_66B48D5B115D4552 (keyword_id), INDEX IDX_66B48D5BD5E258C5 (posts_id), PRIMARY KEY(keyword_id, posts_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE keyword_posts ADD CONSTRAINT FK_66B48D5B115D4552 FOREIGN KEY (keyword_id) REFERENCES keyword (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE keyword_posts ADD CONSTRAINT FK_66B48D5BD5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE keyword_posts DROP FOREIGN KEY FK_66B48D5B115D4552');
        $this->addSql('ALTER TABLE keyword_posts DROP FOREIGN KEY FK_66B48D5BD5E258C5');
        $this->addSql('DROP TABLE keyword_posts');
    }
}
