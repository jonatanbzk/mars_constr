<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200628123022 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD construction_site_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494994A532 FOREIGN KEY (construction_site_id) REFERENCES construction_site (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6494994A532 ON user (construction_site_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494994A532');
        $this->addSql('DROP INDEX IDX_8D93D6494994A532 ON user');
        $this->addSql('ALTER TABLE user DROP construction_site_id');
    }
}
