<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191004133000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (User_Id INT AUTO_INCREMENT NOT NULL, User_Name VARCHAR(50) NOT NULL, User_Login VARCHAR(100) NOT NULL, User_Roles JSON NOT NULL, User_Birthday DATE DEFAULT NULL, User_Avatar_Path VARCHAR(255) DEFAULT NULL, User_Temp_Hash VARCHAR(50) NOT NULL, User_Vk_User_Id INT NOT NULL, User_Vk_Token VARCHAR(255) NOT NULL, User_Vk_Expires_At DATETIME DEFAULT NULL, User_Sex TINYINT(1) NOT NULL, User_SC_Day DATE NOT NULL, User_SC_Time_From TIME NOT NULL, User_SC_Time_To TIME NOT NULL, User_City_Id INT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649C6561C9C (User_Login), INDEX IDX_8D93D64991A9BA63 (User_City_Id), PRIMARY KEY(User_Id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_party (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, status INT NOT NULL, number_of_girls INT NOT NULL, number_of_guys INT NOT NULL, meeting_at DATETIME DEFAULT NULL, meeting_place VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_C88EFFCC71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_party_user (event_party_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_CADECEB567D0F989 (event_party_id), INDEX IDX_CADECEB5A76ED395 (user_id), PRIMARY KEY(event_party_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (City_Id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(City_Id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_party_history (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, event_party_id INT NOT NULL, created_at DATETIME NOT NULL, action SMALLINT NOT NULL, data JSON NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_11125FCCA76ED395 (user_id), INDEX IDX_11125FCC67D0F989 (event_party_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, src VARCHAR(255) NOT NULL, type SMALLINT NOT NULL, alt VARCHAR(255) DEFAULT NULL, video_poster VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, cafe_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, address VARCHAR(255) NOT NULL, reserve_required TINYINT(1) NOT NULL, min_number_of_people SMALLINT NOT NULL, max_number_of_people SMALLINT NOT NULL, site VARCHAR(255) DEFAULT NULL, price VARCHAR(255) DEFAULT NULL, phone VARCHAR(15) DEFAULT NULL, yandex_map_src VARCHAR(255) DEFAULT NULL, timetable_type SMALLINT DEFAULT NULL, duration INT DEFAULT NULL, path_to_cafe_yandex_map_src VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3BAE0AA78BAC62AF (city_id), INDEX IDX_3BAE0AA76710CC07 (cafe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_media (event_id INT NOT NULL, media_id INT NOT NULL, INDEX IDX_2B37102071F7E88B (event_id), INDEX IDX_2B371020EA9FDD75 (media_id), PRIMARY KEY(event_id, media_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cafe (id INT AUTO_INCREMENT NOT NULL, address VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timetable (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, week_day SMALLINT NOT NULL, time_from TIME NOT NULL, time_to TIME NOT NULL, INDEX IDX_6B1F67071F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_party_message (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, event_party_id INT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_80151FF8A76ED395 (user_id), INDEX IDX_80151FF867D0F989 (event_party_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64991A9BA63 FOREIGN KEY (User_City_Id) REFERENCES city (City_Id)');
        $this->addSql('ALTER TABLE event_party ADD CONSTRAINT FK_C88EFFCC71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE event_party_user ADD CONSTRAINT FK_CADECEB567D0F989 FOREIGN KEY (event_party_id) REFERENCES event_party (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_party_user ADD CONSTRAINT FK_CADECEB5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_party_history ADD CONSTRAINT FK_11125FCCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event_party_history ADD CONSTRAINT FK_11125FCC67D0F989 FOREIGN KEY (event_party_id) REFERENCES event_party (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA78BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA76710CC07 FOREIGN KEY (cafe_id) REFERENCES cafe (id)');
        $this->addSql('ALTER TABLE event_media ADD CONSTRAINT FK_2B37102071F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_media ADD CONSTRAINT FK_2B371020EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable ADD CONSTRAINT FK_6B1F67071F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE event_party_message ADD CONSTRAINT FK_80151FF8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event_party_message ADD CONSTRAINT FK_80151FF867D0F989 FOREIGN KEY (event_party_id) REFERENCES event_party (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event_party_user DROP FOREIGN KEY FK_CADECEB5A76ED395');
        $this->addSql('ALTER TABLE event_party_history DROP FOREIGN KEY FK_11125FCCA76ED395');
        $this->addSql('ALTER TABLE event_party_message DROP FOREIGN KEY FK_80151FF8A76ED395');
        $this->addSql('ALTER TABLE event_party_user DROP FOREIGN KEY FK_CADECEB567D0F989');
        $this->addSql('ALTER TABLE event_party_history DROP FOREIGN KEY FK_11125FCC67D0F989');
        $this->addSql('ALTER TABLE event_party_message DROP FOREIGN KEY FK_80151FF867D0F989');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64991A9BA63');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA78BAC62AF');
        $this->addSql('ALTER TABLE event_media DROP FOREIGN KEY FK_2B371020EA9FDD75');
        $this->addSql('ALTER TABLE event_party DROP FOREIGN KEY FK_C88EFFCC71F7E88B');
        $this->addSql('ALTER TABLE event_media DROP FOREIGN KEY FK_2B37102071F7E88B');
        $this->addSql('ALTER TABLE timetable DROP FOREIGN KEY FK_6B1F67071F7E88B');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA76710CC07');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE event_party');
        $this->addSql('DROP TABLE event_party_user');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE event_party_history');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_media');
        $this->addSql('DROP TABLE cafe');
        $this->addSql('DROP TABLE timetable');
        $this->addSql('DROP TABLE event_party_message');
    }
}
