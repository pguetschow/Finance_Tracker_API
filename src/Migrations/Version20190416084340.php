<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190416084340 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('ALTER TABLE entry CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contract CHANGE category_id category_id INT DEFAULT NULL, CHANGE filename filename VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contract CHANGE category_id category_id INT DEFAULT NULL, CHANGE filename filename VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE entry CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
    }
}
