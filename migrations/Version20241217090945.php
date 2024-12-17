<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241217090945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE failed_question (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, failed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_854A8B661E27F6BF (question_id), INDEX IDX_854A8B66B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE failed_question ADD CONSTRAINT FK_854A8B661E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE failed_question ADD CONSTRAINT FK_854A8B66B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE failed_question DROP FOREIGN KEY FK_854A8B661E27F6BF');
        $this->addSql('ALTER TABLE failed_question DROP FOREIGN KEY FK_854A8B66B03A8386');
        $this->addSql('DROP TABLE failed_question');
    }
}
