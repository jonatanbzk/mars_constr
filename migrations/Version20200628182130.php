<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200628182130 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE worker ADD on_site_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE worker ADD CONSTRAINT FK_9FB2BF62B01045EF FOREIGN KEY (on_site_id) REFERENCES construction_site (id)');
        $this->addSql('CREATE INDEX IDX_9FB2BF62B01045EF ON worker (on_site_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE worker DROP FOREIGN KEY FK_9FB2BF62B01045EF');
        $this->addSql('DROP INDEX IDX_9FB2BF62B01045EF ON worker');
        $this->addSql('ALTER TABLE worker DROP on_site_id');
    }
}
