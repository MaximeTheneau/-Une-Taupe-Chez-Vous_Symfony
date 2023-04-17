<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230414115247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(70) DEFAULT NULL, slug VARCHAR(70) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_posts (id INT AUTO_INCREMENT NOT NULL, posts_id INT DEFAULT NULL, title VARCHAR(170) DEFAULT NULL, description VARCHAR(5000) DEFAULT NULL, INDEX IDX_51CE2EF9D5E258C5 (posts_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paragraph_posts (id INT AUTO_INCREMENT NOT NULL, posts_id INT DEFAULT NULL, subtitle VARCHAR(170) DEFAULT NULL, paragraph VARCHAR(5000) DEFAULT NULL, img_post_paragh VARCHAR(500) DEFAULT NULL, alt_img VARCHAR(170) DEFAULT NULL, INDEX IDX_CEE77992D5E258C5 (posts_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, subcategory_id INT DEFAULT NULL, title VARCHAR(70) NOT NULL, slug VARCHAR(70) NOT NULL, contents VARCHAR(5000) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, links VARCHAR(255) DEFAULT NULL, text_links VARCHAR(255) DEFAULT NULL, alt_img VARCHAR(125) DEFAULT NULL, img_post VARCHAR(500) DEFAULT NULL, UNIQUE INDEX UNIQ_885DBAFA2B36786B (title), UNIQUE INDEX UNIQ_885DBAFA989D9B62 (slug), INDEX IDX_885DBAFA12469DE2 (category_id), INDEX IDX_885DBAFA5DC6FE57 (subcategory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts_subtopic (posts_id INT NOT NULL, subtopic_id INT NOT NULL, INDEX IDX_3D3CE908D5E258C5 (posts_id), INDEX IDX_3D3CE90814C59DB4 (subtopic_id), PRIMARY KEY(posts_id, subtopic_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subcategory (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(70) DEFAULT NULL, slug VARCHAR(70) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subtopic (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(70) NOT NULL, slug VARCHAR(70) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE list_posts ADD CONSTRAINT FK_51CE2EF9D5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE paragraph_posts ADD CONSTRAINT FK_CEE77992D5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA5DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES subcategory (id)');
        $this->addSql('ALTER TABLE posts_subtopic ADD CONSTRAINT FK_3D3CE908D5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE posts_subtopic ADD CONSTRAINT FK_3D3CE90814C59DB4 FOREIGN KEY (subtopic_id) REFERENCES subtopic (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE list_posts DROP FOREIGN KEY FK_51CE2EF9D5E258C5');
        $this->addSql('ALTER TABLE paragraph_posts DROP FOREIGN KEY FK_CEE77992D5E258C5');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA12469DE2');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA5DC6FE57');
        $this->addSql('ALTER TABLE posts_subtopic DROP FOREIGN KEY FK_3D3CE908D5E258C5');
        $this->addSql('ALTER TABLE posts_subtopic DROP FOREIGN KEY FK_3D3CE90814C59DB4');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE list_posts');
        $this->addSql('DROP TABLE paragraph_posts');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE posts_subtopic');
        $this->addSql('DROP TABLE subcategory');
        $this->addSql('DROP TABLE subtopic');
    }
}
