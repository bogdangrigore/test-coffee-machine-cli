# test-coffee-machine-cli
A small test Symfony CLI application that simulates a very simple coffee machine.

## Installation
To use this CLI application you will need:
* PHP 7.2 or above
* The SQLite PHP7 extension
* Composer

1. Clone the repo
2. Run composer install.
3. Run php bin/console doctrine:migrations:migrate
4. Add to your .env file the MACHINE_PASSWORD variable. More on that later

## Commands

There are several commands available to interact with the coffee machine

### List command

List command will list all products you can order from the mahcine

**Usage**
`php bin/console coffee:list -a`

The -a option will only show available products

### Order command

You can use the order command to order a hot beverage. 

**Usage** `php bin/console coffee:order-product [--product-name PRODUCT-NAME] [--product-id PRODUCT-ID] [--payment-method PAYMENT-METHOD] [--payment-amount-ron RON-AMOUNT] [--payment-amount-ban BAN-AMOUNT] [--quantity QUANTITY]`

1. You can identify products by either a name or an ID.
2. Payment method can be either cash or card
3. When the payment method is cash you can specify the amount of cash you are placing into the machine. You can put in either RON banknotes (5 or 10 RON banknotes) or coints (50 bani coins)

### Add ingredients command

An admin of the machine can add more ingredients to refill the machine. In order to do that you can use the add ingredients command.

**Usage** `ingredients:add-quantity [--name NAME] [--quantity QUANTITY] [--password PASSWORD]` 

1. The name is the name of the ingredient: coffee, sugar, milk, water
2. Quantity indicates how much of the ingredient you want to load in the machine
3. Password is the value from the MACHINE_PASSWORD. Only by using this password can you access the interface.

### Locking

The machine is locked during usage by a customer. Locking is implemented using the Symfony Lock component.

### Transactions

Each transactions is saved in the local SQLite DB. You can see all transactions using the `transactions:list` command. You need to provid the MACHINE_PASSWORD value to use this command.
