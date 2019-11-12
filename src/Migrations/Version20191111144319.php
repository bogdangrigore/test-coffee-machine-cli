<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191111144319 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, price INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE ingredient (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, quantity INTEGER NOT NULL)');

        $this->addSql('INSERT INTO ingredient (`name`, `quantity`) VALUES ("coffee", 100)');
        $this->addSql('INSERT INTO ingredient (`name`, `quantity`) VALUES ("sugar", 100)');
        $this->addSql('INSERT INTO ingredient (`name`, `quantity`) VALUES ("milk", 500)');
        $this->addSql('INSERT INTO ingredient (`name`, `quantity`) VALUES ("water", 500)');

        $this->addSql('INSERT INTO product (`name`, `price`) VALUES ("Caffe Latte", 800)');
        $this->addSql('INSERT INTO product (`name`, `price`) VALUES ("Espresso", 500)');
        $this->addSql('INSERT INTO product (`name`, `price`) VALUES ("Double Espresso", 700)');
        $this->addSql('INSERT INTO product (`name`, `price`) VALUES ("Cappucino", 1100)');


        $this->addSql('CREATE TABLE product_ingredient (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id_id INTEGER NOT NULL, ingredient_id_id INTEGER NOT NULL, quantity INTEGER)');
        $this->addSql('CREATE INDEX IDX_F8C8275BDE18E50B ON product_ingredient (product_id_id)');
        $this->addSql('CREATE INDEX IDX_F8C8275B6676F996 ON product_ingredient (ingredient_id_id)');

        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Espresso" AND i.name = "coffee"');
        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Espresso" AND i.name = "sugar"');
        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Espresso" AND i.name = "water"');

        $this->addSql('UPDATE product_ingredient SET quantity = "5" WHERE product_id_id = (SELECT id FROM product WHERE name = "Espresso") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "coffee")');
        $this->addSql('UPDATE product_ingredient SET quantity = "2" WHERE product_id_id = (SELECT id FROM product WHERE name = "Espresso") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "sugar")');
        $this->addSql('UPDATE product_ingredient SET quantity = "30" WHERE product_id_id = (SELECT id FROM product WHERE name = "Espresso") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "water")');

        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Caffe Latte" AND i.name = "coffee"');
        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Caffe Latte" AND i.name = "sugar"');
        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Caffe Latte" AND i.name = "water"');
        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Caffe Latte" AND i.name = "milk"');

        $this->addSql('UPDATE product_ingredient SET quantity = "5" WHERE product_id_id = (SELECT id FROM product WHERE name = "Caffe Latte") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "coffee")');
        $this->addSql('UPDATE product_ingredient SET quantity = "2" WHERE product_id_id = (SELECT id FROM product WHERE name = "Caffe Latte") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "sugar")');
        $this->addSql('UPDATE product_ingredient SET quantity = "200" WHERE product_id_id = (SELECT id FROM product WHERE name = "Caffe Latte") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "water")');
        $this->addSql('UPDATE product_ingredient SET quantity = "50" WHERE product_id_id = (SELECT id FROM product WHERE name = "Caffe Latte") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "milk")');

        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Double Espresso" AND i.name = "coffee"');
        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Double Espresso" AND i.name = "sugar"');
        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Double Espresso" AND i.name = "water"');

        $this->addSql('UPDATE product_ingredient SET quantity = "10" WHERE product_id_id = (SELECT id FROM product WHERE name = "Double Espresso") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "coffee")');
        $this->addSql('UPDATE product_ingredient SET quantity = "2" WHERE product_id_id = (SELECT id FROM product WHERE name = "Double Espresso") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "sugar")');
        $this->addSql('UPDATE product_ingredient SET quantity = "30" WHERE product_id_id = (SELECT id FROM product WHERE name = "Double Espresso") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "water")');

        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Cappucino" AND i.name = "coffee"');
        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Cappucino" AND i.name = "sugar"');
        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Cappucino" AND i.name = "water"');
        $this->addSql('INSERT INTO product_ingredient (product_id_id, ingredient_id_id) SELECT p.id, i.id FROM product AS p CROSS JOIN ingredient as i WHERE p.name = "Cappucino" AND i.name = "milk"');

        $this->addSql('UPDATE product_ingredient SET quantity = "5" WHERE product_id_id = (SELECT id FROM product WHERE name = "Cappucino") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "coffee")');
        $this->addSql('UPDATE product_ingredient SET quantity = "2" WHERE product_id_id = (SELECT id FROM product WHERE name = "Cappucino") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "sugar")');
        $this->addSql('UPDATE product_ingredient SET quantity = "150" WHERE product_id_id = (SELECT id FROM product WHERE name = "Cappucino") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "water")');
        $this->addSql('UPDATE product_ingredient SET quantity = "100" WHERE product_id_id = (SELECT id FROM product WHERE name = "Cappucino") AND ingredient_id_id = (SELECT id FROM ingredient WHERE name = "milk")');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite',
            'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE product_ingredient');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE ingredient');
    }
}
