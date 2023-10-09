<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009125916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts_subtopic DROP FOREIGN KEY FK_3D3CE90814C59DB4');
        $this->addSql('ALTER TABLE posts_subtopic DROP FOREIGN KEY FK_3D3CE908D5E258C5');
        $this->addSql('DROP TABLE subtopic');
        $this->addSql('DROP TABLE posts_subtopic');
        $this->addSql('ALTER TABLE posts ADD meta_description VARCHAR(135) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subtopic (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(70) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, slug VARCHAR(70) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE posts_subtopic (posts_id INT NOT NULL, subtopic_id INT NOT NULL, INDEX IDX_3D3CE908D5E258C5 (posts_id), INDEX IDX_3D3CE90814C59DB4 (subtopic_id), PRIMARY KEY(posts_id, subtopic_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE posts_subtopic ADD CONSTRAINT FK_3D3CE90814C59DB4 FOREIGN KEY (subtopic_id) REFERENCES subtopic (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE posts_subtopic ADD CONSTRAINT FK_3D3CE908D5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE posts DROP meta_description');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
