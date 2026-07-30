<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250402070348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD content TEXT NOT NULL, ADD created_at DATETIME NOT NULL, DROP comment, CHANGE parent_comment_id parent_comment_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526C94A4C7D4 FOREIGN KEY (device_id) REFERENCES devices (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526CBF2AF943 FOREIGN KEY (parent_comment_id) REFERENCES comment (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9474526C94A4C7D4 ON comment (device_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9474526CBF2AF943 ON comment (parent_comment_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP FOREIGN KEY FK_9474526C94A4C7D4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP FOREIGN KEY FK_9474526CBF2AF943
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9474526C94A4C7D4 ON comment
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9474526CBF2AF943 ON comment
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD comment VARCHAR(2000) NOT NULL, DROP content, DROP created_at, CHANGE parent_comment_id parent_comment_id INT NOT NULL
        SQL);
    }
}
