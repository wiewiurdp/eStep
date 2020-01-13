<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191115174034 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE batch_booking (batch_id INT NOT NULL, booking_id INT NOT NULL, INDEX IDX_B0DA6784F39EBE7A (batch_id), INDEX IDX_B0DA67843301C60 (booking_id), PRIMARY KEY(batch_id, booking_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_batch (user_id INT NOT NULL, batch_id INT NOT NULL, INDEX IDX_1AC9A98CA76ED395 (user_id), INDEX IDX_1AC9A98CF39EBE7A (batch_id), PRIMARY KEY(user_id, batch_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_booking (user_id INT NOT NULL, booking_id INT NOT NULL, INDEX IDX_B801F3D4A76ED395 (user_id), INDEX IDX_B801F3D43301C60 (booking_id), PRIMARY KEY(user_id, booking_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, batches_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_57698A6ABA3B8694 (batches_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_user (role_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_332CA4DDD60322AC (role_id), INDEX IDX_332CA4DDA76ED395 (user_id), PRIMARY KEY(role_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE batch_booking ADD CONSTRAINT FK_B0DA6784F39EBE7A FOREIGN KEY (batch_id) REFERENCES batch (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE batch_booking ADD CONSTRAINT FK_B0DA67843301C60 FOREIGN KEY (booking_id) REFERENCES booking (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_batch ADD CONSTRAINT FK_1AC9A98CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_batch ADD CONSTRAINT FK_1AC9A98CF39EBE7A FOREIGN KEY (batch_id) REFERENCES batch (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_booking ADD CONSTRAINT FK_B801F3D4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_booking ADD CONSTRAINT FK_B801F3D43301C60 FOREIGN KEY (booking_id) REFERENCES booking (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6ABA3B8694 FOREIGN KEY (batches_id) REFERENCES batch (id)');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE batch DROP user, DROP role, DROP event');
        $this->addSql('ALTER TABLE booking DROP user');
        $this->addSql('ALTER TABLE user DROP batch, DROP role, DROP event');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE role_user DROP FOREIGN KEY FK_332CA4DDD60322AC');
        $this->addSql('DROP TABLE batch_booking');
        $this->addSql('DROP TABLE user_batch');
        $this->addSql('DROP TABLE user_booking');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE role_user');
        $this->addSql('ALTER TABLE batch ADD user INT DEFAULT NULL, ADD role INT DEFAULT NULL, ADD event INT DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD user INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD batch INT DEFAULT NULL, ADD role INT DEFAULT NULL, ADD event INT DEFAULT NULL');
    }
}
