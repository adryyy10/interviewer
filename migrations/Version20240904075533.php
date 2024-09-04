<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240904075533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer CHANGE is_correct correct TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE is_admin `admin` TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer CHANGE correct is_correct TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE `admin` is_admin TINYINT(1) NOT NULL');
    }
}
