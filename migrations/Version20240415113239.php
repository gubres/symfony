<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240415113239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE famosos ADD CONSTRAINT FK_C99BAF10B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C99BAF10B03A8386 ON famosos (created_by_id)');
        $this->addSql('ALTER TABLE user ADD eliminado TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE famosos DROP FOREIGN KEY FK_C99BAF10B03A8386');
        $this->addSql('DROP INDEX IDX_C99BAF10B03A8386 ON famosos');
        $this->addSql('ALTER TABLE user DROP eliminado');
    }
}
