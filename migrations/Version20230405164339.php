<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230405164339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, title VARCHAR(70) NOT NULL, slug VARCHAR(70) NOT NULL, contents VARCHAR(5000) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, links VARCHAR(255) DEFAULT NULL, text_links VARCHAR(255) DEFAULT NULL, alt_img VARCHAR(125) DEFAULT NULL, UNIQUE INDEX UNIQ_BFDD31682B36786B (title), UNIQUE INDEX UNIQ_BFDD3168989D9B62 (slug), INDEX IDX_BFDD316812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_subcategory (articles_id INT NOT NULL, subcategory_id INT NOT NULL, INDEX IDX_A9E993291EBAF6CC (articles_id), INDEX IDX_A9E993295DC6FE57 (subcategory_id), PRIMARY KEY(articles_id, subcategory_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_subtopic (articles_id INT NOT NULL, subtopic_id INT NOT NULL, INDEX IDX_4667C7DB1EBAF6CC (articles_id), INDEX IDX_4667C7DB14C59DB4 (subtopic_id), PRIMARY KEY(articles_id, subtopic_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(70) DEFAULT NULL, slug VARCHAR(70) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faq (id INT AUTO_INCREMENT NOT NULL, question VARCHAR(160) NOT NULL, answer VARCHAR(500) NOT NULL, open TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_articles (id INT AUTO_INCREMENT NOT NULL, articles_id INT DEFAULT NULL, title VARCHAR(170) DEFAULT NULL, description VARCHAR(750) DEFAULT NULL, INDEX IDX_C1B002081EBAF6CC (articles_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pages (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(70) NOT NULL, subtitle VARCHAR(255) DEFAULT NULL, contents VARCHAR(1000) NOT NULL, contents2 VARCHAR(750) DEFAULT NULL, slug VARCHAR(255) NOT NULL, img_header JSON NOT NULL, img_header_jpg VARCHAR(255) NOT NULL, img_header2 JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paragraph_articles (id INT AUTO_INCREMENT NOT NULL, articles_id INT DEFAULT NULL, subtitle VARCHAR(170) DEFAULT NULL, paragraph VARCHAR(5000) DEFAULT NULL, img_post_paragh VARCHAR(500) DEFAULT NULL, INDEX IDX_327A75A91EBAF6CC (articles_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(70) NOT NULL, contents VARCHAR(750) DEFAULT NULL, contents2 VARCHAR(750) DEFAULT NULL, subtitle VARCHAR(70) DEFAULT NULL, slug VARCHAR(255) NOT NULL, img_post JSON NOT NULL, img_post2 JSON DEFAULT NULL, img_post3 JSON DEFAULT NULL, img_post4 JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, subtitle2 VARCHAR(70) DEFAULT NULL, contents3 VARCHAR(750) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subcategory (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(70) DEFAULT NULL, slug VARCHAR(70) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subtopic (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(70) NOT NULL, slug VARCHAR(70) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD316812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE articles_subcategory ADD CONSTRAINT FK_A9E993291EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_subcategory ADD CONSTRAINT FK_A9E993295DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES subcategory (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_subtopic ADD CONSTRAINT FK_4667C7DB1EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_subtopic ADD CONSTRAINT FK_4667C7DB14C59DB4 FOREIGN KEY (subtopic_id) REFERENCES subtopic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE list_articles ADD CONSTRAINT FK_C1B002081EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE paragraph_articles ADD CONSTRAINT FK_327A75A91EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD316812469DE2');
        $this->addSql('ALTER TABLE articles_subcategory DROP FOREIGN KEY FK_A9E993291EBAF6CC');
        $this->addSql('ALTER TABLE articles_subcategory DROP FOREIGN KEY FK_A9E993295DC6FE57');
        $this->addSql('ALTER TABLE articles_subtopic DROP FOREIGN KEY FK_4667C7DB1EBAF6CC');
        $this->addSql('ALTER TABLE articles_subtopic DROP FOREIGN KEY FK_4667C7DB14C59DB4');
        $this->addSql('ALTER TABLE list_articles DROP FOREIGN KEY FK_C1B002081EBAF6CC');
        $this->addSql('ALTER TABLE paragraph_articles DROP FOREIGN KEY FK_327A75A91EBAF6CC');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE articles_subcategory');
        $this->addSql('DROP TABLE articles_subtopic');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE faq');
        $this->addSql('DROP TABLE list_articles');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE paragraph_articles');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE subcategory');
        $this->addSql('DROP TABLE subtopic');
    }
}
