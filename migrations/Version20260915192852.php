<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260915192852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE daily_answer (id INT AUTO_INCREMENT NOT NULL, correct TINYINT NOT NULL, answered_on DATE NOT NULL, user_id INT NOT NULL, question_id INT NOT NULL, choice_id INT NOT NULL, UNIQUE INDEX UNIQ_USER_ANSWERED_ON (user_id, answered_on), INDEX IDX_5BE37D89A76ED395 (user_id), INDEX IDX_5BE37D891E27F6BF (question_id), INDEX IDX_5BE37D89998666D1 (choice_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, prompt VARCHAR(500) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE question_choice (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, correct TINYINT NOT NULL, question_id INT NOT NULL, INDEX IDX_C6F6759A1E27F6BF (question_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_quiz_progress (id INT AUTO_INCREMENT NOT NULL, mood INT NOT NULL, current_streak INT NOT NULL, total_correct_answers INT NOT NULL, last_processed_date DATE DEFAULT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_1A4BA653A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE daily_answer ADD CONSTRAINT FK_5BE37D89A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE daily_answer ADD CONSTRAINT FK_5BE37D891E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE daily_answer ADD CONSTRAINT FK_5BE37D89998666D1 FOREIGN KEY (choice_id) REFERENCES question_choice (id)');
        $this->addSql('ALTER TABLE question_choice ADD CONSTRAINT FK_C6F6759A1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE user_quiz_progress ADD CONSTRAINT FK_1A4BA653A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE daily_answer DROP FOREIGN KEY FK_5BE37D89A76ED395');
        $this->addSql('ALTER TABLE daily_answer DROP FOREIGN KEY FK_5BE37D891E27F6BF');
        $this->addSql('ALTER TABLE daily_answer DROP FOREIGN KEY FK_5BE37D89998666D1');
        $this->addSql('ALTER TABLE question_choice DROP FOREIGN KEY FK_C6F6759A1E27F6BF');
        $this->addSql('ALTER TABLE user_quiz_progress DROP FOREIGN KEY FK_1A4BA653A76ED395');
        $this->addSql('DROP TABLE daily_answer');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE question_choice');
        $this->addSql('DROP TABLE user_quiz_progress');
    }
}
