<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250107103237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change boolean field name from is_correctly_answered to correctly_answered';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE failed_question CHANGE is_correctly_answered correctly_answered TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE failed_question CHANGE correctly_answered is_correctly_answered TINYINT(1) NOT NULL');
    }
}
