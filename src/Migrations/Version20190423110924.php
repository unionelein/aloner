<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190423110924 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event_party CHANGE meeting_point meeting_place VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE search_criteria CHANGE time_from time_from TIME NOT NULL, CHANGE time_to time_to TIME NOT NULL');
        $this->addSql('ALTER TABLE timetable ADD length INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event_party CHANGE meeting_place meeting_point VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE search_criteria CHANGE time_from time_from TIME DEFAULT NULL, CHANGE time_to time_to TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE timetable DROP length');
    }
}
