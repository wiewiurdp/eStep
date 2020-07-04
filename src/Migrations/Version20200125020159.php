<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200125020159 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE presence (id INT AUTO_INCREMENT NOT NULL, stated VARCHAR(255) DEFAULT NULL, actual TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attendee ADD presences_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE attendee ADD CONSTRAINT FK_1150D5677B8B9373 FOREIGN KEY (presences_id) REFERENCES presence (id)');
        $this->addSql('CREATE INDEX IDX_1150D5677B8B9373 ON attendee (presences_id)');
        $this->addSql('ALTER TABLE booking ADD presences_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE7B8B9373 FOREIGN KEY (presences_id) REFERENCES presence (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE7B8B9373 ON booking (presences_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE attendee DROP FOREIGN KEY FK_1150D5677B8B9373');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE7B8B9373');
        $this->addSql('DROP TABLE presence');
        $this->addSql('DROP INDEX IDX_1150D5677B8B9373 ON attendee');
        $this->addSql('ALTER TABLE attendee DROP presences_id');
        $this->addSql('DROP INDEX IDX_E00CEDDE7B8B9373 ON booking');
        $this->addSql('ALTER TABLE booking DROP presences_id');
    }
}
