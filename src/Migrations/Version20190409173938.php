<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190409173938 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE timetable (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, week_day SMALLINT NOT NULL, time_from TIME NOT NULL, time_to TIME NOT NULL, type SMALLINT NOT NULL, INDEX IDX_6B1F67071F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE timetable ADD CONSTRAINT FK_6B1F67071F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE event ADD address VARCHAR(255) DEFAULT NULL, ADD phone VARCHAR(15) DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE media media JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE timetable');
        $this->addSql('ALTER TABLE event DROP address, DROP phone, CHANGE description description LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE media media JSON DEFAULT NULL');
    }
}
