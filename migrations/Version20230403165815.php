<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230403165815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_subcategory (category_id INT NOT NULL, subcategory_id INT NOT NULL, INDEX IDX_BA47E62312469DE2 (category_id), INDEX IDX_BA47E6235DC6FE57 (subcategory_id), PRIMARY KEY(category_id, subcategory_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_subcategory ADD CONSTRAINT FK_BA47E62312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_subcategory ADD CONSTRAINT FK_BA47E6235DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES subcategory (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C11EBAF6CC');
        $this->addSql('DROP INDEX IDX_64C19C11EBAF6CC ON category');
        $this->addSql('ALTER TABLE category DROP articles_id, CHANGE name name VARCHAR(70) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_subcategory DROP FOREIGN KEY FK_BA47E62312469DE2');
        $this->addSql('ALTER TABLE category_subcategory DROP FOREIGN KEY FK_BA47E6235DC6FE57');
        $this->addSql('DROP TABLE category_subcategory');
        $this->addSql('ALTER TABLE category ADD articles_id INT DEFAULT NULL, CHANGE name name VARCHAR(70) NOT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C11EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_64C19C11EBAF6CC ON category (articles_id)');
    }
}
