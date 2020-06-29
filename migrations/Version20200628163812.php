<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200628163812 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE material ADD on_site_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE material ADD CONSTRAINT FK_7CBE7595B01045EF FOREIGN KEY (on_site_id) REFERENCES construction_site (id)');
        $this->addSql('CREATE INDEX IDX_7CBE7595B01045EF ON material (on_site_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE material DROP FOREIGN KEY FK_7CBE7595B01045EF');
        $this->addSql('DROP INDEX IDX_7CBE7595B01045EF ON material');
        $this->addSql('ALTER TABLE material DROP on_site_id');
    }
}
