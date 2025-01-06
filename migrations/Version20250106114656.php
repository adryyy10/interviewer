<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250106114656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add is_correctly_answered column to failed_question table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE failed_question ADD is_correctly_answered TINYINT(1) NOT NULL, CHANGE question_id question_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE failed_question DROP is_correctly_answered, CHANGE question_id question_id INT DEFAULT NULL');
    }
}
