<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230929093540 extends AbstractMigration
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
        $this->addSql('DROP TABLE posts_subtopic');
        $this->addSql('ALTER TABLE posts ADD subtopic_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA14C59DB4 FOREIGN KEY (subtopic_id) REFERENCES subtopic (id)');
        $this->addSql('CREATE INDEX IDX_885DBAFA14C59DB4 ON posts (subtopic_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE posts_subtopic (posts_id INT NOT NULL, subtopic_id INT NOT NULL, INDEX IDX_3D3CE90814C59DB4 (subtopic_id), INDEX IDX_3D3CE908D5E258C5 (posts_id), PRIMARY KEY(posts_id, subtopic_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE posts_subtopic ADD CONSTRAINT FK_3D3CE90814C59DB4 FOREIGN KEY (subtopic_id) REFERENCES subtopic (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE posts_subtopic ADD CONSTRAINT FK_3D3CE908D5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA14C59DB4');
        $this->addSql('DROP INDEX IDX_885DBAFA14C59DB4 ON posts');
        $this->addSql('ALTER TABLE posts DROP subtopic_id');
    }
}
