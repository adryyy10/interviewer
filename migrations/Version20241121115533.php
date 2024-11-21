<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121115533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_answer DROP FOREIGN KEY FK_BF8F5118F24C5BEC');
        $this->addSql('DROP INDEX IDX_BF8F5118F24C5BEC ON user_answer');
        $this->addSql('ALTER TABLE user_answer CHANGE quiz_id quiz_id INT DEFAULT NULL, CHANGE selected_answer_id answer_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F5118AA334807 FOREIGN KEY (answer_id) REFERENCES answer (id)');
        $this->addSql('CREATE INDEX IDX_BF8F5118AA334807 ON user_answer (answer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_answer DROP FOREIGN KEY FK_BF8F5118AA334807');
        $this->addSql('DROP INDEX IDX_BF8F5118AA334807 ON user_answer');
        $this->addSql('ALTER TABLE user_answer CHANGE quiz_id quiz_id INT NOT NULL, CHANGE answer_id selected_answer_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F5118F24C5BEC FOREIGN KEY (selected_answer_id) REFERENCES answer (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_BF8F5118F24C5BEC ON user_answer (selected_answer_id)');
    }
}
