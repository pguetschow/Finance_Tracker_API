<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190305115931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            'CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE user (id VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE entry (id VARCHAR(255) NOT NULL, category_id INT DEFAULT NULL, user_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, INDEX IDX_2B219D7012469DE2 (category_id), INDEX IDX_2B219D70A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE contract (id VARCHAR(255) NOT NULL, category_id INT DEFAULT NULL, user_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, due_interval VARCHAR(255) NOT NULL, filename VARCHAR(255) DEFAULT NULL, INDEX IDX_E98F285912469DE2 (category_id), INDEX IDX_E98F2859A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D7012469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F285912469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F2859A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D7012469DE2');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F285912469DE2');
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D70A76ED395');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F2859A76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE entry');
        $this->addSql('DROP TABLE contract');
    }
}
