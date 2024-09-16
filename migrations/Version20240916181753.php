<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240916181753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add approved in questions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE question ADD approved TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE question DROP approved');
    }
}
