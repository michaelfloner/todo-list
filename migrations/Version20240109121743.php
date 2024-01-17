<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240109121743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TYPE \"enumtaskstate\" AS ENUM('todo', 'in_progress', 'completed', 'deleted');");
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64C19C17E3C61F9 ON category (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C15E237E067E3C61F9 ON category (name, owner_id)');
        $this->addSql('CREATE TABLE task (id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, state enumtaskstate NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527EDB257E3C61F9 ON task (owner_id)');
        $this->addSql('COMMENT ON COLUMN task.state IS \'(DC2Type:enumtaskstate)\'');
        $this->addSql('COMMENT ON COLUMN task.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN task.completed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE task_category (task_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(task_id, category_id))');
        $this->addSql('CREATE INDEX IDX_468CF38D8DB60186 ON task_category (task_id)');
        $this->addSql('CREATE INDEX IDX_468CF38D12469DE2 ON task_category (category_id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C17E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB257E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_category ADD CONSTRAINT FK_468CF38D8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_category ADD CONSTRAINT FK_468CF38D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE "category_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "task_id_seq" CASCADE');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C17E3C61F9');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB257E3C61F9');
        $this->addSql('ALTER TABLE task_category DROP CONSTRAINT FK_468CF38D8DB60186');
        $this->addSql('ALTER TABLE task_category DROP CONSTRAINT FK_468CF38D12469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_category');
    }
}
