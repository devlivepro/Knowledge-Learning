<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250506195122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, cursus_id INT NOT NULL, obtained_at DATETIME NOT NULL, filename VARCHAR(255) DEFAULT NULL, INDEX IDX_6C3C6D75A76ED395 (user_id), INDEX IDX_6C3C6D7540AEF4B9 (cursus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cursus (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, theme_id INT NOT NULL, title VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_255A0C3B03A8386 (created_by_id), INDEX IDX_255A0C3896DBBDE (updated_by_id), INDEX IDX_255A0C359027487 (theme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, cursus_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, video_url VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F87474F3B03A8386 (created_by_id), INDEX IDX_F87474F3896DBBDE (updated_by_id), INDEX IDX_F87474F340AEF4B9 (cursus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_validation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, lesson_id INT NOT NULL, validated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1BA512BFA76ED395 (user_id), INDEX IDX_1BA512BFCDF80196 (lesson_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, cursus_id INT DEFAULT NULL, lesson_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, total DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E52FFDEEA76ED395 (user_id), INDEX IDX_E52FFDEE40AEF4B9 (cursus_id), INDEX IDX_E52FFDEECDF80196 (lesson_id), INDEX IDX_E52FFDEEB03A8386 (created_by_id), INDEX IDX_E52FFDEE896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9775E708B03A8386 (created_by_id), INDEX IDX_9775E708896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_activated TINYINT(1) NOT NULL, verification_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), INDEX IDX_8D93D649B03A8386 (created_by_id), INDEX IDX_8D93D649896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D7540AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)');
        $this->addSql('ALTER TABLE cursus ADD CONSTRAINT FK_255A0C3B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE cursus ADD CONSTRAINT FK_255A0C3896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE cursus ADD CONSTRAINT FK_255A0C359027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F340AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)');
        $this->addSql('ALTER TABLE lesson_validation ADD CONSTRAINT FK_1BA512BFA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE lesson_validation ADD CONSTRAINT FK_1BA512BFCDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE40AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEECDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E708B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E708896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75A76ED395');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D7540AEF4B9');
        $this->addSql('ALTER TABLE cursus DROP FOREIGN KEY FK_255A0C3B03A8386');
        $this->addSql('ALTER TABLE cursus DROP FOREIGN KEY FK_255A0C3896DBBDE');
        $this->addSql('ALTER TABLE cursus DROP FOREIGN KEY FK_255A0C359027487');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3B03A8386');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3896DBBDE');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F340AEF4B9');
        $this->addSql('ALTER TABLE lesson_validation DROP FOREIGN KEY FK_1BA512BFA76ED395');
        $this->addSql('ALTER TABLE lesson_validation DROP FOREIGN KEY FK_1BA512BFCDF80196');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE40AEF4B9');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEECDF80196');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEB03A8386');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE896DBBDE');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY FK_9775E708B03A8386');
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY FK_9775E708896DBBDE');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649B03A8386');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649896DBBDE');
        $this->addSql('DROP TABLE certification');
        $this->addSql('DROP TABLE cursus');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE lesson_validation');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
