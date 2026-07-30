<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250402033539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C18D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C194A4C7D4 FOREIGN KEY (device_id) REFERENCES devices (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_845CA2C18D9F6D38 ON order_details (order_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_845CA2C194A4C7D4 ON order_details (device_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C18D9F6D38
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C194A4C7D4
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_845CA2C18D9F6D38 ON order_details
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_845CA2C194A4C7D4 ON order_details
        SQL);
    }
}
