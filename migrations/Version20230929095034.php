<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230929095034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA14C59DB4');
        $this->addSql('CREATE TABLE keyword (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE keyword_posts (keyword_id INT NOT NULL, posts_id INT NOT NULL, INDEX IDX_66B48D5B115D4552 (keyword_id), INDEX IDX_66B48D5BD5E258C5 (posts_id), PRIMARY KEY(keyword_id, posts_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE keyword_posts ADD CONSTRAINT FK_66B48D5B115D4552 FOREIGN KEY (keyword_id) REFERENCES keyword (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE keyword_posts ADD CONSTRAINT FK_66B48D5BD5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE subtopic');
        $this->addSql('DROP INDEX IDX_885DBAFA14C59DB4 ON posts');
        $this->addSql('ALTER TABLE posts DROP subtopic_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subtopic (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(70) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, slug VARCHAR(70) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE keyword_posts DROP FOREIGN KEY FK_66B48D5B115D4552');
        $this->addSql('ALTER TABLE keyword_posts DROP FOREIGN KEY FK_66B48D5BD5E258C5');
        $this->addSql('DROP TABLE keyword');
        $this->addSql('DROP TABLE keyword_posts');
        $this->addSql('ALTER TABLE posts ADD subtopic_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA14C59DB4 FOREIGN KEY (subtopic_id) REFERENCES subtopic (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_885DBAFA14C59DB4 ON posts (subtopic_id)');
    }
}
