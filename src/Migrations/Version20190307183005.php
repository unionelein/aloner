<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190307183005 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE event_party (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, meeting_at DATETIME DEFAULT NULL, meeting_point VARCHAR(255) DEFAULT NULL, number_of_girls INT NOT NULL, number_of_guys INT NOT NULL, status INT NOT NULL, INDEX IDX_C88EFFCC71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_party_user (event_party_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_CADECEB567D0F989 (event_party_id), INDEX IDX_CADECEB5A76ED395 (user_id), PRIMARY KEY(event_party_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_event_party (user_id INT NOT NULL, event_party_id INT NOT NULL, INDEX IDX_61953659A76ED395 (user_id), INDEX IDX_6195365967D0F989 (event_party_id), PRIMARY KEY(user_id, event_party_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_party ADD CONSTRAINT FK_C88EFFCC71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE event_party_user ADD CONSTRAINT FK_CADECEB567D0F989 FOREIGN KEY (event_party_id) REFERENCES event_party (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_party_user ADD CONSTRAINT FK_CADECEB5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_event_party ADD CONSTRAINT FK_61953659A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_event_party ADD CONSTRAINT FK_6195365967D0F989 FOREIGN KEY (event_party_id) REFERENCES event_party (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD city_id INT NOT NULL, ADD description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA78BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA78BAC62AF ON event (city_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event_party_user DROP FOREIGN KEY FK_CADECEB567D0F989');
        $this->addSql('ALTER TABLE user_event_party DROP FOREIGN KEY FK_6195365967D0F989');
        $this->addSql('DROP TABLE event_party');
        $this->addSql('DROP TABLE event_party_user');
        $this->addSql('DROP TABLE user_event_party');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA78BAC62AF');
        $this->addSql('DROP INDEX IDX_3BAE0AA78BAC62AF ON event');
        $this->addSql('ALTER TABLE event DROP city_id, DROP description');
    }
}
