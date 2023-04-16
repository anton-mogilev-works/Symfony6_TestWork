<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230415213535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, question_id INTEGER NOT NULL, next_question_id INTEGER DEFAULT NULL, text VARCHAR(255) NOT NULL, CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_DADD4A251CF5F25E FOREIGN KEY (next_question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_DADD4A251E27F6BF ON answer (question_id)');
        $this->addSql('CREATE INDEX IDX_DADD4A251CF5F25E ON answer (next_question_id)');
        $this->addSql('CREATE TABLE question (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, text VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE survey_result (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ip_address VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE survey_result_question (survey_result_id INTEGER NOT NULL, question_id INTEGER NOT NULL, PRIMARY KEY(survey_result_id, question_id), CONSTRAINT FK_B9268499A2F1F46E FOREIGN KEY (survey_result_id) REFERENCES survey_result (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B92684991E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B9268499A2F1F46E ON survey_result_question (survey_result_id)');
        $this->addSql('CREATE INDEX IDX_B92684991E27F6BF ON survey_result_question (question_id)');
        $this->addSql('CREATE TABLE survey_result_answer (survey_result_id INTEGER NOT NULL, answer_id INTEGER NOT NULL, PRIMARY KEY(survey_result_id, answer_id), CONSTRAINT FK_5559F281A2F1F46E FOREIGN KEY (survey_result_id) REFERENCES survey_result (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5559F281AA334807 FOREIGN KEY (answer_id) REFERENCES answer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5559F281A2F1F46E ON survey_result_answer (survey_result_id)');
        $this->addSql('CREATE INDEX IDX_5559F281AA334807 ON survey_result_answer (answer_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE survey_result');
        $this->addSql('DROP TABLE survey_result_question');
        $this->addSql('DROP TABLE survey_result_answer');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
