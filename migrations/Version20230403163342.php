<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230403163342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles_category (articles_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_A7D8EFDB1EBAF6CC (articles_id), INDEX IDX_A7D8EFDB12469DE2 (category_id), PRIMARY KEY(articles_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles_category ADD CONSTRAINT FK_A7D8EFDB1EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_category ADD CONSTRAINT FK_A7D8EFDB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_articles_articles DROP FOREIGN KEY FK_F419BDF21EBAF6CC');
        $this->addSql('ALTER TABLE category_articles_articles DROP FOREIGN KEY FK_F419BDF2A921B4A7');
        $this->addSql('DROP TABLE category_articles_articles');
        $this->addSql('DROP TABLE category_articles');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C11EBAF6CC');
        $this->addSql('DROP INDEX IDX_64C19C11EBAF6CC ON category');
        $this->addSql('ALTER TABLE category DROP articles_id, CHANGE name name VARCHAR(70) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_articles_articles (category_articles_id INT NOT NULL, articles_id INT NOT NULL, INDEX IDX_F419BDF21EBAF6CC (articles_id), INDEX IDX_F419BDF2A921B4A7 (category_articles_id), PRIMARY KEY(category_articles_id, articles_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE category_articles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(70) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE category_articles_articles ADD CONSTRAINT FK_F419BDF21EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_articles_articles ADD CONSTRAINT FK_F419BDF2A921B4A7 FOREIGN KEY (category_articles_id) REFERENCES category_articles (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_category DROP FOREIGN KEY FK_A7D8EFDB1EBAF6CC');
        $this->addSql('ALTER TABLE articles_category DROP FOREIGN KEY FK_A7D8EFDB12469DE2');
        $this->addSql('DROP TABLE articles_category');
        $this->addSql('ALTER TABLE category ADD articles_id INT DEFAULT NULL, CHANGE name name VARCHAR(70) NOT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C11EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_64C19C11EBAF6CC ON category (articles_id)');
    }
}
