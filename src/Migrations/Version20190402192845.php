<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190402192845 extends AbstractMigration
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
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, site VARCHAR(255) DEFAULT NULL, media JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3BAE0AA78BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_party_history (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, event_party_id INT NOT NULL, created_at DATETIME NOT NULL, action SMALLINT NOT NULL, INDEX IDX_11125FCCA76ED395 (user_id), INDEX IDX_11125FCC67D0F989 (event_party_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_party_message (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, event_party_id INT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_80151FF8A76ED395 (user_id), INDEX IDX_80151FF867D0F989 (event_party_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE search_criteria (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, time_from TIME DEFAULT NULL, time_to TIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_A6483034A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, login VARCHAR(180) NOT NULL, roles JSON NOT NULL, sex TINYINT(1) DEFAULT NULL, birthday DATE DEFAULT NULL, phone VARCHAR(15) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, temp_hash VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649AA08CB10 (login), INDEX IDX_8D93D6498BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vk_user_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, vk_user_id INT NOT NULL, token VARCHAR(255) NOT NULL, expires_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_F99C362AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_party ADD CONSTRAINT FK_C88EFFCC71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE event_party_user ADD CONSTRAINT FK_CADECEB567D0F989 FOREIGN KEY (event_party_id) REFERENCES event_party (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_party_user ADD CONSTRAINT FK_CADECEB5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA78BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE event_party_history ADD CONSTRAINT FK_11125FCCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event_party_history ADD CONSTRAINT FK_11125FCC67D0F989 FOREIGN KEY (event_party_id) REFERENCES event_party (id)');
        $this->addSql('ALTER TABLE event_party_message ADD CONSTRAINT FK_80151FF8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event_party_message ADD CONSTRAINT FK_80151FF867D0F989 FOREIGN KEY (event_party_id) REFERENCES event_party (id)');
        $this->addSql('ALTER TABLE search_criteria ADD CONSTRAINT FK_A6483034A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE vk_user_token ADD CONSTRAINT FK_F99C362AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event_party_user DROP FOREIGN KEY FK_CADECEB567D0F989');
        $this->addSql('ALTER TABLE event_party_history DROP FOREIGN KEY FK_11125FCC67D0F989');
        $this->addSql('ALTER TABLE event_party_message DROP FOREIGN KEY FK_80151FF867D0F989');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA78BAC62AF');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498BAC62AF');
        $this->addSql('ALTER TABLE event_party DROP FOREIGN KEY FK_C88EFFCC71F7E88B');
        $this->addSql('ALTER TABLE event_party_user DROP FOREIGN KEY FK_CADECEB5A76ED395');
        $this->addSql('ALTER TABLE event_party_history DROP FOREIGN KEY FK_11125FCCA76ED395');
        $this->addSql('ALTER TABLE event_party_message DROP FOREIGN KEY FK_80151FF8A76ED395');
        $this->addSql('ALTER TABLE search_criteria DROP FOREIGN KEY FK_A6483034A76ED395');
        $this->addSql('ALTER TABLE vk_user_token DROP FOREIGN KEY FK_F99C362AA76ED395');
        $this->addSql('DROP TABLE event_party');
        $this->addSql('DROP TABLE event_party_user');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_party_history');
        $this->addSql('DROP TABLE event_party_message');
        $this->addSql('DROP TABLE search_criteria');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vk_user_token');
    }
}
