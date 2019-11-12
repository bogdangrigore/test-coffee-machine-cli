<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191111153228 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE cash (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL)');
        $this->addSql('INSERT INTO cash (`name`, `quantity`) VALUES ("ron", 100)');
        $this->addSql('INSERT INTO cash (`name`, `quantity`) VALUES ("ban", 100)');

        $this->addSql('CREATE TABLE payment_method (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO payment_method (`name`) VALUES ("cash")');
        $this->addSql('INSERT INTO payment_method (`name`) VALUES ("card")');

        $this->addSql('CREATE TABLE order_transaction (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER NOT NULL, payment_method_id INTEGER NOT NULL, quantity INTEGER NOT NULL)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE cash');
        $this->addSql('DROP TABLE payment_method');
        $this->addSql('DROP TABLE "transaction"');
    }
}
