<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250402025303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        
        $this->addSql(<<<'SQL'
            ALTER TABLE devices CHANGE average_rating average_rating DOUBLE PRECISION DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE devices ADD CONSTRAINT FK_11074E9A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_11074E9A12469DE2 ON devices (category_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        
        $this->addSql(<<<'SQL'
            ALTER TABLE devices DROP FOREIGN KEY FK_11074E9A12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_11074E9A12469DE2 ON devices
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE devices CHANGE average_rating average_rating DOUBLE PRECISION NOT NULL
        SQL);
    }
}
