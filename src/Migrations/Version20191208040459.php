<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191208040459 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE role_user DROP FOREIGN KEY FK_332CA4DDA76ED395');
        $this->addSql('ALTER TABLE user_batch DROP FOREIGN KEY FK_1AC9A98CA76ED395');
        $this->addSql('ALTER TABLE user_booking DROP FOREIGN KEY FK_B801F3D4A76ED395');
        $this->addSql('CREATE TABLE attendee (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1150D5675126AC48 (mail), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attendee_batch (attendee_id INT NOT NULL, batch_id INT NOT NULL, INDEX IDX_F03EB766BCFD782A (attendee_id), INDEX IDX_F03EB766F39EBE7A (batch_id), PRIMARY KEY(attendee_id, batch_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attendee_booking (attendee_id INT NOT NULL, booking_id INT NOT NULL, INDEX IDX_E69F3F26BCFD782A (attendee_id), INDEX IDX_E69F3F263301C60 (booking_id), PRIMARY KEY(attendee_id, booking_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_attendee (role_id INT NOT NULL, attendee_id INT NOT NULL, INDEX IDX_3B833356D60322AC (role_id), INDEX IDX_3B833356BCFD782A (attendee_id), PRIMARY KEY(role_id, attendee_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attendee_batch ADD CONSTRAINT FK_F03EB766BCFD782A FOREIGN KEY (attendee_id) REFERENCES attendee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendee_batch ADD CONSTRAINT FK_F03EB766F39EBE7A FOREIGN KEY (batch_id) REFERENCES batch (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendee_booking ADD CONSTRAINT FK_E69F3F26BCFD782A FOREIGN KEY (attendee_id) REFERENCES attendee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendee_booking ADD CONSTRAINT FK_E69F3F263301C60 FOREIGN KEY (booking_id) REFERENCES booking (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_attendee ADD CONSTRAINT FK_3B833356D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_attendee ADD CONSTRAINT FK_3B833356BCFD782A FOREIGN KEY (attendee_id) REFERENCES attendee (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE role_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_batch');
        $this->addSql('DROP TABLE user_booking');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE attendee_batch DROP FOREIGN KEY FK_F03EB766BCFD782A');
        $this->addSql('ALTER TABLE attendee_booking DROP FOREIGN KEY FK_E69F3F26BCFD782A');
        $this->addSql('ALTER TABLE role_attendee DROP FOREIGN KEY FK_3B833356BCFD782A');
        $this->addSql('CREATE TABLE role_user (role_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_332CA4DDD60322AC (role_id), INDEX IDX_332CA4DDA76ED395 (user_id), PRIMARY KEY(role_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, surname VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, mail VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, address VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, UNIQUE INDEX UNIQ_8D93D6495126AC48 (mail), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_batch (user_id INT NOT NULL, batch_id INT NOT NULL, INDEX IDX_1AC9A98CA76ED395 (user_id), INDEX IDX_1AC9A98CF39EBE7A (batch_id), PRIMARY KEY(user_id, batch_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_booking (user_id INT NOT NULL, booking_id INT NOT NULL, INDEX IDX_B801F3D4A76ED395 (user_id), INDEX IDX_B801F3D43301C60 (booking_id), PRIMARY KEY(user_id, booking_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_batch ADD CONSTRAINT FK_1AC9A98CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_batch ADD CONSTRAINT FK_1AC9A98CF39EBE7A FOREIGN KEY (batch_id) REFERENCES batch (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_booking ADD CONSTRAINT FK_B801F3D43301C60 FOREIGN KEY (booking_id) REFERENCES booking (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_booking ADD CONSTRAINT FK_B801F3D4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE attendee');
        $this->addSql('DROP TABLE attendee_batch');
        $this->addSql('DROP TABLE attendee_booking');
        $this->addSql('DROP TABLE role_attendee');
    }
}
