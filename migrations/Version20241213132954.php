<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241213132954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement ADD name_fr VARCHAR(255) DEFAULT NULL, ADD name_en VARCHAR(255) DEFAULT NULL, ADD name_de VARCHAR(255) DEFAULT NULL, ADD description_fr LONGTEXT DEFAULT NULL, ADD description_en LONGTEXT DEFAULT NULL, ADD description_de LONGTEXT DEFAULT NULL, DROP name, DROP description');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement ADD name VARCHAR(255) NOT NULL, ADD description LONGTEXT NOT NULL, DROP name_fr, DROP name_en, DROP name_de, DROP description_fr, DROP description_en, DROP description_de');
    }
}
