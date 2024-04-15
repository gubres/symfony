<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240415090724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE famosos DROP FOREIGN KEY fk_famosos_created_by_id');
        $this->addSql('ALTER TABLE famosos DROP FOREIGN KEY fk_famosos_updated_by_id');
        $this->addSql('DROP INDEX fk_famosos_created_by_id ON famosos');
        $this->addSql('DROP INDEX fk_famosos_updated_by_id ON famosos');
        $this->addSql('ALTER TABLE famosos DROP created_by, DROP updated_by');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE famosos ADD created_by INT NOT NULL, ADD updated_by INT NOT NULL');
        $this->addSql('ALTER TABLE famosos ADD CONSTRAINT fk_famosos_created_by_id FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE famosos ADD CONSTRAINT fk_famosos_updated_by_id FOREIGN KEY (updated_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX fk_famosos_created_by_id ON famosos (created_by)');
        $this->addSql('CREATE INDEX fk_famosos_updated_by_id ON famosos (updated_by)');
    }
}
