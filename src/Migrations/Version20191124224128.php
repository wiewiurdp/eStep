<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191124224128 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6ABA3B8694');
        $this->addSql('DROP INDEX IDX_57698A6ABA3B8694 ON role');
        $this->addSql('ALTER TABLE role CHANGE batches_id batch_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6AF39EBE7A FOREIGN KEY (batch_id) REFERENCES batch (id)');
        $this->addSql('CREATE INDEX IDX_57698A6AF39EBE7A ON role (batch_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6AF39EBE7A');
        $this->addSql('DROP INDEX IDX_57698A6AF39EBE7A ON role');
        $this->addSql('ALTER TABLE role CHANGE batch_id batches_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6ABA3B8694 FOREIGN KEY (batches_id) REFERENCES batch (id)');
        $this->addSql('CREATE INDEX IDX_57698A6ABA3B8694 ON role (batches_id)');
    }
}
