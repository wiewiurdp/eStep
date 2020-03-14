<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200212002941 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE attendee_presence (attendee_id INT NOT NULL, presence_id INT NOT NULL, INDEX IDX_DA172E59BCFD782A (attendee_id), INDEX IDX_DA172E59F328FFC4 (presence_id), PRIMARY KEY(attendee_id, presence_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attendee_presence ADD CONSTRAINT FK_DA172E59BCFD782A FOREIGN KEY (attendee_id) REFERENCES attendee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendee_presence ADD CONSTRAINT FK_DA172E59F328FFC4 FOREIGN KEY (presence_id) REFERENCES presence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendee DROP FOREIGN KEY FK_1150D5677B8B9373');
        $this->addSql('DROP INDEX IDX_1150D5677B8B9373 ON attendee');
        $this->addSql('ALTER TABLE attendee DROP presences_id');
        $this->addSql('ALTER TABLE presence ADD booking_id INT DEFAULT NULL, ADD attendee_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE presence ADD CONSTRAINT FK_6977C7A53301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE presence ADD CONSTRAINT FK_6977C7A5BCFD782A FOREIGN KEY (attendee_id) REFERENCES attendee (id)');
        $this->addSql('CREATE INDEX IDX_6977C7A53301C60 ON presence (booking_id)');
        $this->addSql('CREATE INDEX IDX_6977C7A5BCFD782A ON presence (attendee_id)');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE7B8B9373');
        $this->addSql('DROP INDEX IDX_E00CEDDE7B8B9373 ON booking');
        $this->addSql('ALTER TABLE booking DROP presences_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE attendee_presence');
        $this->addSql('ALTER TABLE attendee ADD presences_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE attendee ADD CONSTRAINT FK_1150D5677B8B9373 FOREIGN KEY (presences_id) REFERENCES presence (id)');
        $this->addSql('CREATE INDEX IDX_1150D5677B8B9373 ON attendee (presences_id)');
        $this->addSql('ALTER TABLE booking ADD presences_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE7B8B9373 FOREIGN KEY (presences_id) REFERENCES presence (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE7B8B9373 ON booking (presences_id)');
        $this->addSql('ALTER TABLE presence DROP FOREIGN KEY FK_6977C7A53301C60');
        $this->addSql('ALTER TABLE presence DROP FOREIGN KEY FK_6977C7A5BCFD782A');
        $this->addSql('DROP INDEX IDX_6977C7A53301C60 ON presence');
        $this->addSql('DROP INDEX IDX_6977C7A5BCFD782A ON presence');
        $this->addSql('ALTER TABLE presence DROP booking_id, DROP attendee_id');
    }
}
