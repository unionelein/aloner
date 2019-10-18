<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191018094255 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (user_id INT AUTO_INCREMENT NOT NULL, user_city_id INT DEFAULT NULL, user_name VARCHAR(50) NOT NULL, user_login VARCHAR(100) NOT NULL, user_roles JSON NOT NULL, user_vk_user_id INT DEFAULT NULL, user_vk_token VARCHAR(255) DEFAULT NULL, user_vk_expires_at DATETIME DEFAULT NULL, user_birthday DATE DEFAULT NULL, user_avatar_path VARCHAR(255) DEFAULT NULL, user_sc_day DATE DEFAULT NULL, user_sc_time_from TIME DEFAULT NULL, user_sc_time_to TIME DEFAULT NULL, user_temp_hash VARCHAR(50) NOT NULL, user_Sex TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64948CA3048 (user_login), INDEX IDX_8D93D649D3A17CA5 (user_city_id), PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_party (ep_id INT AUTO_INCREMENT NOT NULL, ep_event_id INT NOT NULL, ep_status INT NOT NULL, ep_meeting_at DATETIME DEFAULT NULL, ep_meeting_place VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, ep_num_of_girls INT NOT NULL, ep_num_of_guys INT NOT NULL, INDEX IDX_C88EFFCC573B74B (ep_event_id), PRIMARY KEY(ep_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ep_user_map (epu_ep_id INT NOT NULL, epu_user_id INT NOT NULL, INDEX IDX_C3F94F0BCC840F8 (epu_ep_id), INDEX IDX_C3F94F0B07D6515 (epu_user_id), PRIMARY KEY(epu_ep_id, epu_user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (city_id INT AUTO_INCREMENT NOT NULL, city_name VARCHAR(255) NOT NULL, PRIMARY KEY(city_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ep_history (history_id INT AUTO_INCREMENT NOT NULL, history_user_id INT NOT NULL, history_ep_id INT NOT NULL, history_related_history_id INT DEFAULT NULL, history_created_at DATETIME NOT NULL, history_action SMALLINT NOT NULL, history_data JSON NOT NULL, deleted_at DATETIME DEFAULT NULL, dtype VARCHAR(255) NOT NULL, INDEX IDX_12372AC88309FF7F (history_user_id), INDEX IDX_12372AC83C77BBF9 (history_ep_id), INDEX IDX_12372AC8410D145E (history_related_history_id), PRIMARY KEY(history_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ep_message (message_id INT AUTO_INCREMENT NOT NULL, message_user_id INT NOT NULL, message_ep_id INT NOT NULL, message_message LONGTEXT NOT NULL, message_created_at DATETIME NOT NULL, INDEX IDX_83306AFC37E6E999 (message_user_id), INDEX IDX_83306AFC9A694C75 (message_ep_id), PRIMARY KEY(message_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (media_id INT AUTO_INCREMENT NOT NULL, src VARCHAR(255) NOT NULL, type SMALLINT NOT NULL, alt VARCHAR(255) DEFAULT NULL, video_poster VARCHAR(255) DEFAULT NULL, PRIMARY KEY(media_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (event_id INT AUTO_INCREMENT NOT NULL, event_city_id INT DEFAULT NULL, event_name VARCHAR(255) NOT NULL, event_desc LONGTEXT DEFAULT NULL, event_reservation_req TINYINT(1) NOT NULL, event_price VARCHAR(20) DEFAULT NULL, event_duration INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, event_address VARCHAR(100) NOT NULL, event_site VARCHAR(100) DEFAULT NULL, event_phone VARCHAR(30) DEFAULT NULL, event_yandex_map VARCHAR(255) DEFAULT NULL, event_people_min INT NOT NULL, event_people_max INT NOT NULL, INDEX IDX_3BAE0AA7EFBCB00 (event_city_id), PRIMARY KEY(event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_media_map (emp_event_id INT NOT NULL, emp_media_id INT NOT NULL, INDEX IDX_5E69E67B1F133A95 (emp_event_id), INDEX IDX_5E69E67B847B0F6B (emp_media_id), PRIMARY KEY(emp_event_id, emp_media_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timetable (timetable_id INT AUTO_INCREMENT NOT NULL, timetable_event_id INT NOT NULL, timetable_type SMALLINT NOT NULL, timetable_week_day SMALLINT NOT NULL, timetable_time_from TIME NOT NULL, timetable_time_to TIME NOT NULL, INDEX IDX_6B1F670C6CBD4FA (timetable_event_id), PRIMARY KEY(timetable_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D3A17CA5 FOREIGN KEY (user_city_id) REFERENCES city (city_id)');
        $this->addSql('ALTER TABLE event_party ADD CONSTRAINT FK_C88EFFCC573B74B FOREIGN KEY (ep_event_id) REFERENCES event (event_id)');
        $this->addSql('ALTER TABLE ep_user_map ADD CONSTRAINT FK_C3F94F0BCC840F8 FOREIGN KEY (epu_ep_id) REFERENCES event_party (ep_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ep_user_map ADD CONSTRAINT FK_C3F94F0B07D6515 FOREIGN KEY (epu_user_id) REFERENCES user (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ep_history ADD CONSTRAINT FK_12372AC88309FF7F FOREIGN KEY (history_user_id) REFERENCES user (user_id)');
        $this->addSql('ALTER TABLE ep_history ADD CONSTRAINT FK_12372AC83C77BBF9 FOREIGN KEY (history_ep_id) REFERENCES event_party (ep_id)');
        $this->addSql('ALTER TABLE ep_history ADD CONSTRAINT FK_12372AC8410D145E FOREIGN KEY (history_related_history_id) REFERENCES ep_history (history_id)');
        $this->addSql('ALTER TABLE ep_message ADD CONSTRAINT FK_83306AFC37E6E999 FOREIGN KEY (message_user_id) REFERENCES user (user_id)');
        $this->addSql('ALTER TABLE ep_message ADD CONSTRAINT FK_83306AFC9A694C75 FOREIGN KEY (message_ep_id) REFERENCES event_party (ep_id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7EFBCB00 FOREIGN KEY (event_city_id) REFERENCES city (city_id)');
        $this->addSql('ALTER TABLE event_media_map ADD CONSTRAINT FK_5E69E67B1F133A95 FOREIGN KEY (emp_event_id) REFERENCES event (event_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_media_map ADD CONSTRAINT FK_5E69E67B847B0F6B FOREIGN KEY (emp_media_id) REFERENCES media (media_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable ADD CONSTRAINT FK_6B1F670C6CBD4FA FOREIGN KEY (timetable_event_id) REFERENCES event (event_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ep_user_map DROP FOREIGN KEY FK_C3F94F0B07D6515');
        $this->addSql('ALTER TABLE ep_history DROP FOREIGN KEY FK_12372AC88309FF7F');
        $this->addSql('ALTER TABLE ep_message DROP FOREIGN KEY FK_83306AFC37E6E999');
        $this->addSql('ALTER TABLE ep_user_map DROP FOREIGN KEY FK_C3F94F0BCC840F8');
        $this->addSql('ALTER TABLE ep_history DROP FOREIGN KEY FK_12372AC83C77BBF9');
        $this->addSql('ALTER TABLE ep_message DROP FOREIGN KEY FK_83306AFC9A694C75');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D3A17CA5');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7EFBCB00');
        $this->addSql('ALTER TABLE ep_history DROP FOREIGN KEY FK_12372AC8410D145E');
        $this->addSql('ALTER TABLE event_media_map DROP FOREIGN KEY FK_5E69E67B847B0F6B');
        $this->addSql('ALTER TABLE event_party DROP FOREIGN KEY FK_C88EFFCC573B74B');
        $this->addSql('ALTER TABLE event_media_map DROP FOREIGN KEY FK_5E69E67B1F133A95');
        $this->addSql('ALTER TABLE timetable DROP FOREIGN KEY FK_6B1F670C6CBD4FA');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE event_party');
        $this->addSql('DROP TABLE ep_user_map');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE ep_history');
        $this->addSql('DROP TABLE ep_message');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_media_map');
        $this->addSql('DROP TABLE timetable');
    }
}
