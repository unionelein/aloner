<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190502145817 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7D3F350D');
        $this->addSql('DROP INDEX IDX_3BAE0AA7D3F350D ON event');
        $this->addSql('ALTER TABLE event ADD reserve_required TINYINT(1) NOT NULL, ADD timetable_type SMALLINT DEFAULT NULL, ADD duration INT DEFAULT NULL, DROP slug, CHANGE description description LONGTEXT NOT NULL, CHANGE address address VARCHAR(255) NOT NULL, CHANGE near_cafe_id cafe_id INT DEFAULT NULL, CHANGE price_text price VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA76710CC07 FOREIGN KEY (cafe_id) REFERENCES cafe (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA76710CC07 ON event (cafe_id)');
        $this->addSql('ALTER TABLE event_party ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE event_party_history ADD deleted_at DATETIME DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE timetable DROP type, DROP length');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA76710CC07');
        $this->addSql('DROP INDEX IDX_3BAE0AA76710CC07 ON event');
        $this->addSql('ALTER TABLE event ADD near_cafe_id INT DEFAULT NULL, ADD slug VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP cafe_id, DROP reserve_required, DROP timetable_type, DROP duration, CHANGE description description LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE address address VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE price price_text VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7D3F350D FOREIGN KEY (near_cafe_id) REFERENCES cafe (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7D3F350D ON event (near_cafe_id)');
        $this->addSql('ALTER TABLE event_party DROP deleted_at');
        $this->addSql('ALTER TABLE event_party_history DROP deleted_at, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE timetable ADD type SMALLINT NOT NULL, ADD length INT NOT NULL');
    }
}
